<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpecialistController extends Controller
{
    public function index()
    {
        $specialists = Specialist::orderBy('sort_order')->get();
        return Inertia::render('Admin/Specialists/Index', [
            'specialists' => $specialists,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Specialists/Form', [
            'specialist' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'qualification' => 'required|string|max:150',
            'location' => 'required|string|max:150',
            'pincode' => 'nullable|string|max:10',
            'city' => 'required|string|in:ahmedabad,gandhinagar,surat,rajkot',
            'languages' => 'nullable|array',
            'badge' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'photo' => 'nullable|string',
            'photo_file' => 'nullable|image|max:3072',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('specialists', 'public');
            $validated['photo'] = 'storage/' . $path;
        }

        Specialist::create($validated);

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist added successfully.');
    }

    public function edit($id)
    {
        $specialist = Specialist::findOrFail($id);
        return Inertia::render('Admin/Specialists/Form', [
            'specialist' => $specialist,
        ]);
    }

    public function update(Request $request, $id)
    {
        $specialist = Specialist::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'qualification' => 'required|string|max:150',
            'location' => 'required|string|max:150',
            'pincode' => 'nullable|string|max:10',
            'city' => 'required|string|in:ahmedabad,gandhinagar,surat,rajkot',
            'languages' => 'nullable|array',
            'badge' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'photo' => 'nullable|string',
            'photo_file' => 'nullable|image|max:3072',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('specialists', 'public');
            $validated['photo'] = 'storage/' . $path;
        }

        $specialist->update($validated);

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist updated successfully.');
    }

    public function destroy($id)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->delete();

        return redirect()->route('admin.specialists.index')->with('success', 'Specialist deleted successfully.');
    }
}
