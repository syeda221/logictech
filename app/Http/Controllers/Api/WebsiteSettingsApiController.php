<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebsiteSettingsApiController extends Controller
{
    public function index()
    {
        // Cache website settings for 10 minutes for lightning-fast loading
        $settings = Cache::remember('api_website_settings_group', 600, function () {
            return Setting::where('group', 'website')->get();
        });

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }
}
