<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    use HasFactory;

    protected $fillable = [
        'pincode',
        'area_name',
        'city',
        'state',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Find all pincodes within a given radius (in km) from a lat/lng coordinate
     * Uses the Haversine spherical distance formula
     */
    public static function findInRadius(float $lat, float $lng, float $radiusKm = 10.0)
    {
        // 6371 is the Earth's radius in kilometers
        $haversine = "(6371 * acos(
            least(1.0, greatest(-1.0,
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            ))
        ))";

        return static::selectRaw("*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance', 'asc')
            ->get();
    }
}

