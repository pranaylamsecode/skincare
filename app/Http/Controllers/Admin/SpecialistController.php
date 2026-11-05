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
    public function index()
    {
        $specialists = Specialist::with('user:id,email,specialist_id')->orderBy('sort_order')->get();
        return Inertia::render('Admin/Specialists/Index', [
            'specialists' => $specialists,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Specialists/Form', [
            'specialist' => null,
            'staffUser' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'qualification' => 'required|string|max:150',
            'location' => 'required|string|max:150',
            'pincode' => 'nullable|string|max:255',
            'serviceable_pincodes' => 'nullable|string|max:1000',
            'city' => 'required|string|in:ahmedabad,gandhinagar,surat,rajkot',
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

    public function edit($id)
    {
        $specialist = Specialist::with('user:id,email,specialist_id')->findOrFail($id);
        return Inertia::render('Admin/Specialists/Form', [
            'specialist' => $specialist,
            'staffUser' => $specialist->user ? ['email' => $specialist->user->email] : null,
        ]);
    }

    public function update(Request $request, $id)
    {
        $specialist = Specialist::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'qualification' => 'required|string|max:150',
            'location' => 'required|string|max:150',
            'pincode' => 'nullable|string|max:255',
            'serviceable_pincodes' => 'nullable|string|max:1000',
            'city' => 'required|string|in:ahmedabad,gandhinagar,surat,rajkot',
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

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist updated successfully.');
    }

    public function destroy($id)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->delete();

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist deleted successfully.');
    }
}
