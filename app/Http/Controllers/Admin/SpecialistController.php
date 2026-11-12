<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SpecialistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // If logged in as staff, immediately redirect to their own profile
        if ($user && $user->isStaff()) {
            if ($user->specialist_id) {
                return redirect()->route('admin.specialists.edit', $user->specialist_id);
            }
            abort(403, 'Your staff account is not linked to a specialist profile.');
        }

        $specialists = Specialist::with('user:id,email,specialist_id')->orderBy('sort_order')->get();
        return Inertia::render('Admin/Specialists/Index', [
            'specialists' => $specialists,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user && $user->isStaff()) {
            abort(403, 'Staff members cannot create new specialists.');
        }

        return Inertia::render('Admin/Specialists/Form', [
            'specialist' => null,
            'staffUser' => null,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user && $user->isStaff()) {
            abort(403, 'Staff members cannot create new specialists.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'qualification' => 'required|string|max:150',
            'location' => 'required|string|max:150',
            'pincode' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'service_radius_km' => 'nullable|integer|min:1|max:100',
            'serviceable_pincodes' => 'nullable|string|max:1000',
            'city' => 'required|string|in:ahmedabad,gandhinagar,surat,rajkot',
            'gender' => 'required|string|in:female,male,other',
            'languages' => 'nullable|array',
            'badge' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'photo' => 'nullable|string',
            'photo_file' => 'nullable|image|max:3072',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'staff_email' => 'nullable|email|max:100',
            'staff_password' => 'nullable|string|min:6|max:100',
        ]);

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('specialists', 'public');
            $validated['photo'] = 'storage/' . $path;
        }

        $specialist = Specialist::create($validated);

        // Create or update Staff Login User
        $staffEmail = $request->filled('staff_email') ? $request->staff_email : $request->email;
        if ($staffEmail) {
            $user = User::where('specialist_id', $specialist->id)->first() 
                ?? User::where('email', $staffEmail)->first() 
                ?? new User();

            $user->name = $specialist->name;
            $user->email = $staffEmail;
            $user->role = 'staff';
            $user->specialist_id = $specialist->id;

            if ($request->filled('staff_password')) {
                $user->password = Hash::make($request->staff_password);
            } elseif (!$user->exists) {
                $user->password = Hash::make('staff123'); // Default password
            }
            $user->save();
        }

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist added successfully.');
    }

    public function edit(Request $request, $id)
    {
        $user = $request->user();
        if ($user && $user->isStaff()) {
            // Staff can ONLY access their own specialist profile
            if ((int) $user->specialist_id !== (int) $id) {
                return redirect()->route('admin.specialists.edit', $user->specialist_id)
                    ->with('error', 'You can only view and manage your own specialist profile.');
            }
        }

        $specialist = Specialist::with('user:id,email,specialist_id')->findOrFail($id);
        return Inertia::render('Admin/Specialists/Form', [
            'specialist' => $specialist,
            'staffUser' => $specialist->user ? ['email' => $specialist->user->email] : null,
            'isStaff' => $user ? $user->isStaff() : false,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($user && $user->isStaff()) {
            // Staff can ONLY update their own specialist profile
            if ((int) $user->specialist_id !== (int) $id) {
                abort(403, 'Unauthorized. You can only update your own specialist profile.');
            }
        }

        $specialist = Specialist::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'qualification' => 'required|string|max:150',
            'location' => 'required|string|max:150',
            'pincode' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'service_radius_km' => 'nullable|integer|min:1|max:100',
            'serviceable_pincodes' => 'nullable|string|max:1000',
            'city' => 'required|string|in:ahmedabad,gandhinagar,surat,rajkot',
            'gender' => 'required|string|in:female,male,other',
            'languages' => 'nullable|array',
            'badge' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'photo' => 'nullable|string',
            'photo_file' => 'nullable|image|max:3072',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'staff_email' => 'nullable|email|max:100',
            'staff_password' => 'nullable|string|min:6|max:100',
        ]);

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('specialists', 'public');
            $validated['photo'] = 'storage/' . $path;
        }

        $specialist->update($validated);

        // Update Staff Login User
        $staffEmail = $request->filled('staff_email') ? $request->staff_email : $request->email;
        if ($staffEmail) {
            $user = User::where('specialist_id', $specialist->id)->first() 
                ?? User::where('email', $staffEmail)->first() 
                ?? new User();

            $user->name = $specialist->name;
            $user->email = $staffEmail;
            $user->role = 'staff';
            $user->specialist_id = $specialist->id;

            if ($request->filled('staff_password')) {
                $user->password = Hash::make($request->staff_password);
            } elseif (!$user->exists) {
                $user->password = Hash::make('staff123');
            }
            $user->save();
        }

        if ($user && $user->isStaff()) {
            return redirect()->route('admin.specialists.edit', $id)->with('success', 'Your profile and service area were updated successfully.');
        }

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if ($user && $user->isStaff()) {
            abort(403, 'Staff members cannot delete specialists.');
        }

        $specialist = Specialist::findOrFail($id);
        $specialist->delete();

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist deleted successfully.');
    }
}
