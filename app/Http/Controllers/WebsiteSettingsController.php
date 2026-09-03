<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class WebsiteSettingsController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('website-settings.view')) {
            abort(403, 'Unauthorized action. You do not have permission to view Website Settings.');
        }

        $settings = Setting::where('group', 'website')->pluck('value', 'key')->toArray();
        $categories = \App\Models\Category::all();
        
        return view('admin_panel.website_settings.index', compact('settings', 'categories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'facebook_link' => 'nullable|url',
            'instagram_link' => 'nullable|url',
            'tiktok_link' => 'nullable|url',
            'whatsapp_number' => 'nullable|string|max:50',
            'shipping_policy' => 'nullable|string',
            'return_policy' => 'nullable|string',
            'about_us' => 'nullable|string',
            'home_banner_text' => 'nullable|string',
            'easypaisa_account_title' => 'nullable|string|max:255',
            'easypaisa_mobile_number' => 'nullable|string|max:50',
            'home_hero_media_type' => 'nullable|string|in:video,image',
            'store_locator_map_iframe' => 'nullable|string',
            'store_locator_locations' => 'nullable|string',
        ]);
        // 1. Check Edit/Update Permission generally
        if (!auth()->user()->hasAnyPermission(['website-settings.edit', 'website-settings.update'])) {
            abort(403, 'Unauthorized action. You do not have permission to edit or update Website Settings.');
        }

        // 2. Check Upload/Manage Permission if files are uploaded
        $hasFiles = $request->hasFile('site_logo') || $request->hasFile('home_banner_image') || 
                    $request->hasFile('home_hero_video') || $request->hasFile('home_hero_image') || 
                    $request->hasFile('easypaisa_qr_code') || $request->hasFile('store_locator_banner_image');

        if ($hasFiles && !auth()->user()->hasPermissionTo('website-settings.upload_manage')) {
            return redirect()->back()->with('error', 'Unauthorized action. You need upload/manage permission to upload files.');
        }

        $keys = [
            'site_name', 'contact_email', 'contact_phone',
            'facebook_link', 'instagram_link', 'tiktok_link', 'whatsapp_number',
            'shipping_policy', 'return_policy', 'about_us', 'home_banner_text',
            'easypaisa_account_title', 'easypaisa_mobile_number',
            'home_hero_media_type', 'store_locator_map_iframe', 'store_locator_locations'
        ];

        // 3. Check Create and Delete Permissions for text keys
        foreach ($keys as $key) {
            $oldValue = Setting::where('key', 'web_' . $key)->value('value');
            $newValue = $request->input($key, '');

            // Deleting a setting value (was filled, now clearing it)
            if (!empty($oldValue) && empty($newValue)) {
                if (!auth()->user()->hasPermissionTo('website-settings.delete')) {
                    return redirect()->back()->with('error', 'Unauthorized action. You need delete permission to clear the value of ' . ucwords(str_replace('_', ' ', $key)) . '.');
                }
            }

            // Creating a setting value (was empty, now filling it)
            if (empty($oldValue) && !empty($newValue)) {
                if (!auth()->user()->hasPermissionTo('website-settings.create')) {
                    return redirect()->back()->with('error', 'Unauthorized action. You need create permission to set ' . ucwords(str_replace('_', ' ', $key)) . '.');
                }
            }
        }

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => 'web_' . $key],
                [
                    'value' => $request->input($key, ''),
                    'type' => 'string',
                    'group' => 'website'
                ]
            );
            Cache::forget('setting_web_' . $key);
        }

        if ($request->hasFile('home_banner_image')) {
            $file = $request->file('home_banner_image');
            $fileName = time() . '_banner.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $fileName);
            
            Setting::updateOrCreate(
                ['key' => 'web_home_banner_image'],
                [
                    'value' => 'uploads/settings/' . $fileName,
                    'type' => 'string',
                    'group' => 'website'
                ]
            );
            Cache::forget('setting_web_home_banner_image');
        }

        if ($request->hasFile('home_hero_video')) {
            $file = $request->file('home_hero_video');
            $fileName = time() . '_hero_video.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $fileName);
            
            Setting::updateOrCreate(
                ['key' => 'web_home_hero_video'],
                [
                    'value' => 'uploads/settings/' . $fileName,
                    'type' => 'string',
                    'group' => 'website'
                ]
            );
            Cache::forget('setting_web_home_hero_video');
        }

        if ($request->hasFile('home_hero_image')) {
            $file = $request->file('home_hero_image');
            $fileName = time() . '_hero_image.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $fileName);
            
            Setting::updateOrCreate(
                ['key' => 'web_home_hero_image'],
                [
                    'value' => 'uploads/settings/' . $fileName,
                    'type' => 'string',
                    'group' => 'website'
                ]
            );
            Cache::forget('setting_web_home_hero_image');
        }

        if ($request->hasFile('site_logo')) {
            $file = $request->file('site_logo');
            $fileName = time() . '_logo.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $fileName);
            
            Setting::updateOrCreate(
                ['key' => 'web_site_logo'],
                [
                    'value' => 'uploads/settings/' . $fileName,
                    'type' => 'string',
                    'group' => 'website'
                ]
            );
            Cache::forget('setting_web_site_logo');
        }

        if ($request->hasFile('easypaisa_qr_code')) {
            $file = $request->file('easypaisa_qr_code');
            $fileName = time() . '_easypaisa_qr.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $fileName);
            
            Setting::updateOrCreate(
                ['key' => 'web_easypaisa_qr_code'],
                [
                    'value' => 'uploads/settings/' . $fileName,
                    'type' => 'string',
                    'group' => 'website'
                ]
            );
            Cache::forget('setting_web_easypaisa_qr_code');
        }

        if ($request->hasFile('store_locator_banner_image')) {
            $file = $request->file('store_locator_banner_image');
            $fileName = time() . '_store_locator_banner.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $fileName);
            
            Setting::updateOrCreate(
                ['key' => 'web_store_locator_banner_image'],
                [
                    'value' => 'uploads/settings/' . $fileName,
                    'type' => 'string',
                    'group' => 'website'
                ]
            );
            Cache::forget('setting_web_store_locator_banner_image');
        }

        return redirect()->back()->with('success', 'Website settings updated successfully.');
    }

    public function updateCategories(Request $request)
    {
        // 1. Check Edit/Update Permission generally
        if (!auth()->user()->hasAnyPermission(['website-settings.edit', 'website-settings.update'])) {
            abort(403, 'Unauthorized action. You do not have permission to edit or update Category Settings.');
        }

        // 2. Check Upload/Manage Permission if files are uploaded
        $hasCatFiles = false;
        if ($request->has('categories')) {
            foreach ($request->input('categories') as $catId => $data) {
                if ($request->hasFile("categories.{$catId}.web_image")) {
                    $hasCatFiles = true;
                    break;
                }
            }
        }

        if ($hasCatFiles && !auth()->user()->hasPermissionTo('website-settings.upload_manage')) {
            return redirect()->back()->with('error', 'Unauthorized action. You need upload/manage permission to upload files.');
        }

        $categories = \App\Models\Category::all();

        foreach ($categories as $category) {
            $showOnWebsite = $request->has("categories.{$category->id}.show_on_website");
            $category->show_on_website = $showOnWebsite;

            if ($request->hasFile("categories.{$category->id}.web_image")) {
                $file = $request->file("categories.{$category->id}.web_image");
                $fileName = time() . "_cat_{$category->id}." . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/categories'), $fileName);
                $category->web_image = 'uploads/categories/' . $fileName;
            }

            $category->save();
        }

        Cache::forget('api_website_categories');
        Cache::forget('api_web_categories');

        return redirect()->back()->with('success', 'Category website settings updated successfully.');
    }
}
