<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('Super Admin') && !$user->can('settings.view') && !$user->can('settings.edit')) {
            abort(403, 'Unauthorized action. You do not have permission to view ERP Settings.');
        }

        $settings = Setting::getAllGrouped();
        
        return view('admin_panel.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            if ($user && !$user->hasRole('Super Admin') && !$user->can('settings.edit') && !$user->can('settings.create')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action. You do not have permission to edit ERP Settings.',
                ], 403);
            }

            $validated = $request->validate([
                'settings' => 'nullable|array',
                'company_logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
                'remove_company_logo' => 'nullable|string',
            ]);

            if (!empty($validated['settings'])) {
                foreach ($validated['settings'] as $key => $value) {
                    if ($key === 'company_logo') continue;
                    Setting::set($key, $value);
                }
            }

            // Handle company logo removal or update
            if ($request->input('remove_company_logo') == '1' || $request->input('remove_company_logo') === 'true') {
                $oldLogo = Setting::get('company_logo') ?: Setting::get('web_site_logo');
                if ($oldLogo && file_exists(public_path($oldLogo))) {
                    @unlink(public_path($oldLogo));
                }
                if ($oldLogo && file_exists(base_path($oldLogo))) {
                    @unlink(base_path($oldLogo));
                }
                Setting::set('company_logo', null, 'company', 'image', 'Company Logo', 'Logo displayed at the top of receipts and invoices');
                Setting::set('web_site_logo', null, 'website', 'string', 'Site Logo', 'Website Logo');
                Cache::forget('setting_company_logo');
                Cache::forget('setting_web_site_logo');
            } elseif ($request->hasFile('company_logo')) {
                $file = $request->file('company_logo');
                $fileName = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/settings');
                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $fileName);
                $logoPath = 'uploads/settings/' . $fileName;

                // Also copy to root uploads/settings if root uploads exists (e.g. shared host cPanel setups)
                if (file_exists(base_path('uploads'))) {
                    $rootSettingsDir = base_path('uploads/settings');
                    if (!file_exists($rootSettingsDir)) {
                        @mkdir($rootSettingsDir, 0777, true);
                    }
                    @copy($destinationPath . '/' . $fileName, $rootSettingsDir . '/' . $fileName);
                }

                // Also copy to public_html/uploads/settings if exists (cPanel setups)
                if (file_exists(base_path('public_html'))) {
                    $cpanelSettingsDir = base_path('public_html/uploads/settings');
                    if (!file_exists($cpanelSettingsDir)) {
                        @mkdir($cpanelSettingsDir, 0777, true);
                    }
                    @copy($destinationPath . '/' . $fileName, $cpanelSettingsDir . '/' . $fileName);
                }

                // Remove old logo if exists
                $oldLogo = Setting::get('company_logo');
                if ($oldLogo && $oldLogo !== $logoPath) {
                    if (file_exists(public_path($oldLogo))) @unlink(public_path($oldLogo));
                    if (file_exists(base_path($oldLogo))) @unlink(base_path($oldLogo));
                }

                Setting::set('company_logo', $logoPath, 'company', 'image', 'Company Logo', 'Logo displayed at the top of receipts and invoices');
                Setting::set('web_site_logo', $logoPath, 'website', 'string', 'Site Logo', 'Website Logo');
                Cache::forget('setting_company_logo');
                Cache::forget('setting_web_site_logo');
            }

            $currentLogo = Setting::get('company_logo') ?: Setting::get('web_site_logo');

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'logo_url' => $currentLogo ? asset(ltrim($currentLogo, '/')) : null,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', \Illuminate\Support\Arr::flatten($ve->errors())),
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Settings update error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display return policy settings page
     */
    public function returnSettings()
    {
        if (!auth()->user()->hasAnyPermission(['settings.view', 'settings.read'])) {
            abort(403, 'Unauthorized action. You do not have permission to view Return Policy Settings.');
        }

        $settings = \App\Models\SystemSetting::where('group', 'returns')->get();
        
        return view('admin_panel.settings.return_policy', compact('settings'));
    }

    /**
     * Update return policy settings
     */
    public function updateReturnSettings(Request $request)
    {
        if (!auth()->user()->hasAnyPermission(['settings.edit', 'settings.update'])) {
            abort(403, 'Unauthorized action. You do not have permission to edit Return Policy Settings.');
        }

        $validated = $request->validate([
            'return_deadline_days' => 'required|integer|min:0|max:365',
            'return_require_approval' => 'nullable|boolean',
            'return_auto_approve_threshold' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            \App\Models\SystemSetting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Return policy settings updated successfully!');
    }

    /**
     * Show return approvers management page
     */
    public function returnApprovers()
    {
        if (!auth()->user()->hasAnyPermission(['settings.view', 'settings.read'])) {
            abort(403, 'Unauthorized action. You do not have permission to view Return Approvers Settings.');
        }

        $users = \App\Models\User::with('roles')
            ->where('id', '!=', auth()->id()) // Exclude current user
            ->orderBy('name')
            ->get();
        
        return view('admin_panel.settings.return_approvers', compact('users'));
    }

    /**
     * Update return approval permissions for users
     */
    public function updateReturnApprovers(Request $request)
    {
        if (!auth()->user()->hasAnyPermission(['settings.edit', 'settings.update'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action. You do not have permission to edit Return Approvers Settings.',
            ], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'can_approve_returns' => 'nullable|boolean',
            'can_approve_past_deadline_returns' => 'nullable|boolean',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);
        
        $user->can_approve_returns = $request->has('can_approve_returns');
        $user->can_approve_past_deadline_returns = $request->has('can_approve_past_deadline_returns');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Permissions updated for {$user->name}",
        ]);
    }

    /**
     * Get notifications for current user
     */
    public function notifications()
    {
        $notifications = SystemNotification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin_panel.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notification count
     */
    public function notificationCount()
    {
        $count = SystemNotification::getUnreadCount(Auth::id());
        
        return response()->json(['count' => $count]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = SystemNotification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        SystemNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }
}
