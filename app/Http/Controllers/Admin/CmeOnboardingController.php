<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmeOnboarding;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CmeOnboardingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $programme = $request->query('programme', 'all');
        $search = $request->query('search', '');

        $query = CmeOnboarding::query();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($programme && $programme !== 'all') {
            $query->whereJsonContains('programmes', $programme);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%")
                  ->orWhere('registration_id', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('pincode', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $applications = [];
        $counts = [
            'total' => 0,
            'pending' => 0,
            'reviewing' => 0,
            'interview_scheduled' => 0,
            'approved' => 0,
            'rejected' => 0,
        ];

        try {
            $applications = $query->orderBy('created_at', 'desc')->get();
            $all = CmeOnboarding::all();
            $counts = [
                'total' => $all->count(),
                'pending' => $all->where('status', 'pending')->count(),
                'reviewing' => $all->where('status', 'reviewing')->count(),
                'interview_scheduled' => $all->where('status', 'interview_scheduled')->count(),
                'approved' => $all->where('status', 'approved')->count(),
                'rejected' => $all->where('status', 'rejected')->count(),
            ];
        } catch (\Throwable $e) {
            // Table fallback for initial setup
            $applications = [];
        }

        return Inertia::render('Admin/CmeOnboarding/Index', [
            'applications' => $applications,
            'counts' => $counts,
            'filters' => [
                'status' => $status,
                'programme' => $programme,
                'search' => $search,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,interview_scheduled,approved,rejected',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $application = CmeOnboarding::findOrFail($id);
        $application->update($validated);

        return back()->with('success', "Application #{$application->registration_id} updated successfully.");
    }

    public function promoteToSpecialist(Request $request, $id)
    {
        $application = CmeOnboarding::findOrFail($id);

        $qualification = $application->nursing_qualification ?: $application->highest_qualification;
        if (empty($qualification)) {
            $qualification = 'Certified Skincare Specialist';
        }

        $specialist = Specialist::create([
            'name' => $application->full_name,
            'email' => $application->email,
            'phone' => $application->mobile,
            'qualification' => $qualification,
            'location' => "{$application->city} ({$application->pincode})",
            'pincode' => $application->pincode,
            'city' => strtolower($application->city),
            'languages' => ['Gujarati', 'Hindi', 'English'],
            'badge' => 'Verified Specialist',
            'gender' => strtolower($application->gender) === 'male' ? 'male' : 'female',
            'is_active' => true,
        ]);

        $application->update([
            'status' => 'approved',
            'admin_notes' => trim(($application->admin_notes ?? '') . "\nPromoted to Specialist #{$specialist->id} on " . now()->toFormattedDateString()),
        ]);

        return redirect()->route('admin.specialists.edit', $specialist->id)
            ->with('success', "CME {$application->full_name} has been promoted to Specialist successfully!");
    }

    public function destroy($id)
    {
        $application = CmeOnboarding::findOrFail($id);
        $application->delete();

        return back()->with('success', 'Application deleted successfully.');
    }
}

