<?php

namespace App\Http\Controllers;

use App\Models\LayoutSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class LayoutSettingController extends Controller
{
    /**
     * Display the layout settings form
     */
    public function index()
    {
        $settings = LayoutSetting::getActive();

        // Create default settings if none exist
        if (!$settings) {
            $settings = LayoutSetting::create([
                'admin_app_name' => config('app.name', 'Admin Panel'),
                'frontend_app_name' => 'MyShop',
                'admin_icon' => 'fas fa-shield-halved',
                'frontend_icon' => 'fas fa-store',
            ]);
        }

        return view('admin.layout-settings.index', compact('settings'));
    }

    /**
     * Update the layout settings
     */
    public function update(Request $request)
    {
        $settings = LayoutSetting::getActive();

        if (!$settings) {
            $settings = LayoutSetting::create([]);
        }

        $validated = $request->validate([
            // Admin Settings
            'admin_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'admin_favicon' => 'nullable|image|mimes:ico,png,jpg|max:512',
            'admin_app_name' => 'required|string|max:255',
            'admin_icon' => 'nullable|string|max:100',

            // Frontend Settings
            'frontend_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'frontend_favicon' => 'nullable|image|mimes:ico,png,jpg|max:512',
            'frontend_app_name' => 'required|string|max:255',
            'frontend_icon' => 'nullable|string|max:100',

            // Header/Title Settings
            'site_title' => 'nullable|string|max:255',
            'title_bg_color' => 'nullable|string|max:7',
            'title_text_color' => 'nullable|string|max:7',

            // Logo Display Settings
            'logo_size' => 'nullable|integer|min:20|max:200',
            'footer_logo_size' => 'nullable|integer|min:20|max:100',

            // Contact Information
            'contact_email' => 'nullable|array',
            'contact_email.*' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|array',
            'contact_phone.*' => 'nullable|string|max:20',

            // Footer Settings
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'footer_text' => 'nullable|string|max:500',
            'footer_bg_color' => 'nullable|string|max:7',
            'footer_text_color' => 'nullable|string|max:7',

            // Social Links
            'social_icon' => 'nullable|array',
            'social_icon.*' => 'nullable|string|max:100',
            'social_title' => 'nullable|array',
            'social_title.*' => 'nullable|string|max:100',
            'social_url' => 'nullable|array',
            'social_url.*' => 'nullable|url|max:255',
        ]);

        // Handle Admin Logo Upload
        if ($request->hasFile('admin_logo')) {
            $settings->deleteOldFile('admin_logo');
            $validated['admin_logo'] = $request->file('admin_logo')
                ->store('logos/admin', 'public');
        }

        // Handle Admin Favicon Upload
        if ($request->hasFile('admin_favicon')) {
            $settings->deleteOldFile('admin_favicon');
            $validated['admin_favicon'] = $request->file('admin_favicon')
                ->store('favicons/admin', 'public');
        }

        // Handle Frontend Logo Upload
        if ($request->hasFile('frontend_logo')) {
            $settings->deleteOldFile('frontend_logo');
            $validated['frontend_logo'] = $request->file('frontend_logo')
                ->store('logos/frontend', 'public');
        }

        // Handle Frontend Favicon Upload
        if ($request->hasFile('frontend_favicon')) {
            $settings->deleteOldFile('frontend_favicon');
            $validated['frontend_favicon'] = $request->file('frontend_favicon')
                ->store('favicons/frontend', 'public');
        }

        // Handle Footer Logo Upload
        if ($request->hasFile('footer_logo')) {
            $settings->deleteOldFile('footer_logo_path');
            $validated['footer_logo_path'] = $request->file('footer_logo')
                ->store('logos/footer', 'public');
        }

        // Prepare social links array
        $socialLinks = [];
        if ($request->has('social_url')) {
            foreach ($request->social_url as $key => $url) {
                if ($url) {
                    $socialLinks[] = [
                        'icon' => $request->social_icon[$key] ?? 'fab fa-link',
                        'title' => $request->social_title[$key] ?? '',
                        'url' => $url,
                    ];
                }
            }
        }

        $validated['social_links'] = $socialLinks;

        // Update settings
        $settings->update($validated);

        return redirect()
            ->route('layout-settings.index')
            ->with('success', 'Layout settings updated successfully!');
    }

    /**
     * Delete a specific logo/favicon
     */
    public function deleteLogo(Request $request, $type)
    {
        $settings = LayoutSetting::getActive();

        $allowedTypes = [
            'admin_logo',
            'admin_favicon',
            'frontend_logo',
            'frontend_favicon',
            'footer_logo_path'
        ];

        if (!in_array($type, $allowedTypes)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        if ($settings->$type) {
            $settings->deleteOldFile($type);
            $settings->update([$type => null]);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
        }

        return response()->json(['error' => 'No file to delete'], 404);
    }
}
