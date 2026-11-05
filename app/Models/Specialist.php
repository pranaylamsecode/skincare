<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'qualification',
        'location',
        'pincode',
        'serviceable_pincodes',
        'city',
        'languages',
        'badge',
        'photo',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'languages' => 'array',
        'is_active' => 'boolean',
    ];

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all serviceable pincodes for this specialist as an array of trimmed strings
     */
    public function getAllPincodes(): array
    {
        $pincodes = [];

        // 1. From serviceable_pincodes column (comma or space separated)
        if (!empty($this->serviceable_pincodes)) {
            $parts = preg_split('/[,\s]+/', $this->serviceable_pincodes);
            foreach ($parts as $p) {
                $p = trim($p);
                if (strlen($p) === 6 && ctype_digit($p)) {
                    $pincodes[] = $p;
                }
            }
        }

        // 2. From primary pincode column
        if (!empty($this->pincode)) {
            $parts = preg_split('/[,\s]+/', $this->pincode);
            foreach ($parts as $p) {
                $p = trim($p);
                if (strlen($p) === 6 && ctype_digit($p)) {
                    $pincodes[] = $p;
                }
            }
        }

        // 3. Extract any 6-digit pincode mentioned in location text (e.g. "Paldi (380007)")
        if (!empty($this->location) && preg_match_all('/\b(3[0-9]{5})\b/', $this->location, $matches)) {
            foreach ($matches[1] as $p) {
                $pincodes[] = $p;
            }
        }

        return array_values(array_unique($pincodes));
    }

    /**
     * Check if specialist serves a particular 6-digit pincode
     */
    public function servesPincode(string $targetPincode): bool
    {
        $target = trim($targetPincode);
        if (strlen($target) !== 6) {
            return false;
        }

        return in_array($target, $this->getAllPincodes(), true);
    }

    /**
     * Retrieve all active specialists that serve a given pincode
     */
    public static function getSpecialistsForPincode(string $pincode)
    {
        $target = trim($pincode);
        return static::where('is_active', true)
            ->get()
            ->filter(fn(Specialist $sp) => $sp->servesPincode($target))
            ->values();
    }

    /**
     * Round-robin auto-assign specialist for a given pincode, date, and time slot
     * 
     * Returns:
     * [
     *   'serviceable' => bool,
     *   'specialist' => Specialist|null,
     *   'candidates_count' => int,
     *   'reason' => string|null, // 'unserviceable' | 'slot_booked'
     * ]
     */
    public static function findAvailableForPincode(string $pincode, string $date, ?int $timeSlotId = null): array
    {
        $target = trim($pincode);
        $servingSpecialists = static::getSpecialistsForPincode($target);

        if ($servingSpecialists->isEmpty()) {
            return [
                'serviceable' => false,
                'specialist' => null,
                'candidates_count' => 0,
                'reason' => 'unserviceable',
            ];
        }

        // Check which specialists already have a booking on this date & slot
        $bookedSpecialistIds = Booking::where('booking_date', $date)
            ->when($timeSlotId, function ($q) use ($timeSlotId) {
                $q->where('time_slot_id', $timeSlotId);
            })
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->pluck('specialist_id')
            ->filter()
            ->toArray();

        // Candidates who are free for this slot
        $freeCandidates = $servingSpecialists->reject(function ($sp) use ($bookedSpecialistIds) {
            return in_array($sp->id, $bookedSpecialistIds, true);
        })->values();

        if ($freeCandidates->isEmpty()) {
            return [
                'serviceable' => true,
                'specialist' => null,
                'candidates_count' => $servingSpecialists->count(),
                'reason' => 'slot_booked',
            ];
        }

        // If only 1 candidate available, assign directly
        if ($freeCandidates->count() === 1) {
            return [
                'serviceable' => true,
                'specialist' => $freeCandidates->first(),
                'candidates_count' => 1,
                'reason' => null,
            ];
        }

        // Multiple candidates available in the same area:
        // Round-Robin Load Balancer:
        // Prioritize specialist with the fewest bookings on this date,
        // then fewest total active bookings, then sort_order, then ID.
        $sortedCandidates = $freeCandidates->sortBy(function ($sp) use ($date) {
            $dayBookings = Booking::where('specialist_id', $sp->id)
                ->where('booking_date', $date)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->count();

            $totalActiveBookings = Booking::where('specialist_id', $sp->id)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->count();

            return sprintf('%04d-%04d-%04d-%04d', $dayBookings, $totalActiveBookings, $sp->sort_order ?? 0, $sp->id);
        })->values();

        return [
            'serviceable' => true,
            'specialist' => $sortedCandidates->first(),
            'candidates_count' => $freeCandidates->count(),
            'reason' => null,
        ];
    }
}
