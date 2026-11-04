<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concern;
use App\Models\Device;
use App\Models\Faq;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Content/Index', [
            'plans' => Plan::orderBy('sort_order')->get(),
            'devices' => Device::orderBy('sort_order')->get(),
            'products' => Product::orderBy('sort_order')->get(),
            'concerns' => Concern::orderBy('sort_order')->get(),
            'reviews' => Review::orderBy('sort_order')->get(),
            'faqs' => Faq::orderBy('sort_order')->get(),
        ]);
    }

    // Plans
    public function savePlan(Request $request, $id = null)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'tag' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'price_subtext' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($id) {
            Plan::findOrFail($id)->update($validated);
        } else {
            Plan::create($validated);
        }

        return back()->with('success', 'Plan saved successfully.');
    }

    public function deletePlan($id)
    {
        Plan::findOrFail($id)->delete();
        return back()->with('success', 'Plan deleted successfully.');
    }

    // Devices
    public function saveDevice(Request $request, $id = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'pill' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'features' => 'nullable|array',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($id) {
            Device::findOrFail($id)->update($validated);
        } else {
            Device::create($validated);
        }

        return back()->with('success', 'Device saved successfully.');
    }

    public function deleteDevice($id)
    {
        Device::findOrFail($id)->delete();
        return back()->with('success', 'Device deleted successfully.');
    }

    // Products
    public function saveProduct(Request $request, $id = null)
    {
        $validated = $request->validate([
            'step_name' => 'nullable|string|max:50',
            'name' => 'required|string|max:150',
            'category' => 'required|in:daily,wellness',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($id) {
            Product::findOrFail($id)->update($validated);
        } else {
            Product::create($validated);
        }

        return back()->with('success', 'Product saved successfully.');
    }

    public function deleteProduct($id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Product deleted successfully.');
    }

    // FAQs
    public function saveFaq(Request $request, $id = null)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:2000',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($id) {
            Faq::findOrFail($id)->update($validated);
        } else {
            Faq::create($validated);
        }

        return back()->with('success', 'FAQ saved successfully.');
    }

    public function deleteFaq($id)
    {
        Faq::findOrFail($id)->delete();
        return back()->with('success', 'FAQ deleted successfully.');
    }
}
