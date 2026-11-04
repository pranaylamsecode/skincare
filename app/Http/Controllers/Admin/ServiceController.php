<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->get();
        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Services/Form', [
            'service' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'tag' => 'nullable|string|max:100',
            'tag_style' => 'required|in:normal,tan,dark',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'launch_label' => 'nullable|string|max:50',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:3072',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('services', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return Inertia::render('Admin/Services/Form', [
            'service' => $service,
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'tag' => 'nullable|string|max:100',
            'tag_style' => 'required|in:normal,tan,dark',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'launch_label' => 'nullable|string|max:50',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:3072',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('services', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
