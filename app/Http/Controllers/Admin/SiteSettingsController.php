<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    public function index(): View
    {
        $settings = SiteSettings::getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,webp|max:512',
            'banner_background' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:1000',
            'whatsapp_number' => 'nullable|string|max:20',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'footer_text' => 'nullable|string|max:1000',
        ]);

        // Handle checkbox - if not present, set to false
        $validated['show_site_name'] = $request->has('show_site_name');

        $settings = SiteSettings::getSettings();

        if ($request->hasFile('logo')) {
            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }
            $validated['logo'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($settings->favicon) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        if ($request->hasFile('banner_background')) {
            if ($settings->banner_background) {
                Storage::disk('public')->delete($settings->banner_background);
            }
            $validated['banner_background'] = $request->file('banner_background')->store('settings', 'public');
        }

        $settings->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Site settings updated successfully.');
    }
}
