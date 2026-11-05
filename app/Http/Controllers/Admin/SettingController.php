<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        if (auth()->user()?->isStaff()) {
            return redirect()->route('admin.time-slots.index')->with('error', 'Staff members have access to their assigned bookings and time slots.');
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Default icons if not set
        if (empty($settings['site_favicon'])) {
            $settings['site_favicon'] = 'images/FMZ Glow logo.jpg';
        }
        if (empty($settings['site_logo'])) {
            $settings['site_logo'] = 'images/FMZ Glow logo.jpg';
        }

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        if (auth()->user()?->isStaff()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'favicon_file' => 'nullable|file|mimes:ico,png,svg,jpg,jpeg|max:2048',
            'logo_file' => 'nullable|image|max:4096',
        ]);

        // Handle Favicon file upload
        if ($request->hasFile('favicon_file')) {
            $file = $request->file('favicon_file');
            $filename = 'favicon_' . time() . '.' . ($file->getClientOriginalExtension() ?: 'ico');
            $destination = public_path('branding');
            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            Setting::set('site_favicon', 'branding/' . $filename);
        }

        // Handle Logo file upload
        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('branding');
            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            Setting::set('site_logo', 'branding/' . $filename);
        }

        $data = $request->except(['_token', 'favicon_file', 'logo_file']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return back()->with('success', 'Settings and brand icons updated successfully.');
    }
}
