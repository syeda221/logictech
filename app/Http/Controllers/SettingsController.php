<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PDO;

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
                'company_stamp' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
                'company_signature' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
                'remove_company_logo' => 'nullable|string',
                'remove_company_stamp' => 'nullable|string',
                'remove_company_signature' => 'nullable|string',
            ]);

            if (!empty($validated['settings'])) {
                foreach ($validated['settings'] as $key => $value) {
                    if (in_array($key, ['company_logo', 'company_stamp', 'company_signature'])) continue;
                    Setting::set($key, $value);
                }

                // Keep website settings in sync with company settings
                $syncMap = [
                    'company_address' => 'web_contact_address',
                    'company_name'    => 'web_site_name',
                    'company_phone'   => 'web_contact_phone',
                    'company_email'   => 'web_contact_email',
                ];
                foreach ($syncMap as $companyKey => $webKey) {
                    if (isset($validated['settings'][$companyKey])) {
                        Setting::set($webKey, $validated['settings'][$companyKey], 'website', 'string');
                    }
                }
            }

            // Handle company logo removal or update
            if ($request->input('remove_company_logo') == '1' || $request->input('remove_company_logo') === 'true') {
                $oldLogo = Setting::get('company_logo') ?: Setting::get('web_site_logo');
                if ($oldLogo && file_exists(public_path($oldLogo))) @unlink(public_path($oldLogo));
                if ($oldLogo && file_exists(base_path($oldLogo))) @unlink(base_path($oldLogo));
                Setting::set('company_logo', null, 'company', 'image', 'Company Logo', 'Logo displayed at the top of receipts and invoices');
                Setting::set('web_site_logo', null, 'website', 'string', 'Site Logo', 'Website Logo');
                Cache::forget('setting_company_logo');
                Cache::forget('setting_web_site_logo');
            } elseif ($request->hasFile('company_logo')) {
                $file = $request->file('company_logo');
                $fileName = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/settings');
                if (!file_exists($destinationPath)) @mkdir($destinationPath, 0777, true);
                $file->move($destinationPath, $fileName);
                $logoPath = 'uploads/settings/' . $fileName;

                if (file_exists(base_path('uploads'))) {
                    $rootSettingsDir = base_path('uploads/settings');
                    if (!file_exists($rootSettingsDir)) @mkdir($rootSettingsDir, 0777, true);
                    @copy($destinationPath . '/' . $fileName, $rootSettingsDir . '/' . $fileName);
                }

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

            // Handle company stamp removal or update
            if ($request->input('remove_company_stamp') == '1' || $request->input('remove_company_stamp') === 'true') {
                $oldStamp = Setting::get('company_stamp');
                if ($oldStamp && file_exists(public_path($oldStamp))) @unlink(public_path($oldStamp));
                if ($oldStamp && file_exists(base_path($oldStamp))) @unlink(base_path($oldStamp));
                Setting::set('company_stamp', null, 'company', 'image', 'Company Stamp', 'Official digital company stamp');
                Cache::forget('setting_company_stamp');
            } elseif ($request->hasFile('company_stamp')) {
                $file = $request->file('company_stamp');
                $fileName = 'company_stamp_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/settings');
                if (!file_exists($destinationPath)) @mkdir($destinationPath, 0777, true);
                $file->move($destinationPath, $fileName);
                $stampPath = 'uploads/settings/' . $fileName;

                if (file_exists(base_path('uploads'))) {
                    $rootSettingsDir = base_path('uploads/settings');
                    if (!file_exists($rootSettingsDir)) @mkdir($rootSettingsDir, 0777, true);
                    @copy($destinationPath . '/' . $fileName, $rootSettingsDir . '/' . $fileName);
                }

                $oldStamp = Setting::get('company_stamp');
                if ($oldStamp && $oldStamp !== $stampPath) {
                    if (file_exists(public_path($oldStamp))) @unlink(public_path($oldStamp));
                    if (file_exists(base_path($oldStamp))) @unlink(base_path($oldStamp));
                }

                Setting::set('company_stamp', $stampPath, 'company', 'image', 'Company Stamp', 'Official digital company stamp');
                Cache::forget('setting_company_stamp');
            }

            // Handle company signature removal or update
            if ($request->input('remove_company_signature') == '1' || $request->input('remove_company_signature') === 'true') {
                $oldSig = Setting::get('company_signature');
                if ($oldSig && file_exists(public_path($oldSig))) @unlink(public_path($oldSig));
                if ($oldSig && file_exists(base_path($oldSig))) @unlink(base_path($oldSig));
                Setting::set('company_signature', null, 'company', 'image', 'Authorized Signature', 'Official E-Signature');
                Cache::forget('setting_company_signature');
            } elseif ($request->hasFile('company_signature')) {
                $file = $request->file('company_signature');
                $fileName = 'company_signature_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/settings');
                if (!file_exists($destinationPath)) @mkdir($destinationPath, 0777, true);
                $file->move($destinationPath, $fileName);
                $sigPath = 'uploads/settings/' . $fileName;

                if (file_exists(base_path('uploads'))) {
                    $rootSettingsDir = base_path('uploads/settings');
                    if (!file_exists($rootSettingsDir)) @mkdir($rootSettingsDir, 0777, true);
                    @copy($destinationPath . '/' . $fileName, $rootSettingsDir . '/' . $fileName);
                }

                $oldSig = Setting::get('company_signature');
                if ($oldSig && $oldSig !== $sigPath) {
                    if (file_exists(public_path($oldSig))) @unlink(public_path($oldSig));
                    if (file_exists(base_path($oldSig))) @unlink(base_path($oldSig));
                }

                Setting::set('company_signature', $sigPath, 'company', 'image', 'Authorized Signature', 'Official E-Signature');
                Cache::forget('setting_company_signature');
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'logo_url' => Setting::getLogoUrl(),
                'stamp_url' => Setting::getStampUrl(),
                'signature_url' => Setting::getSignatureUrl(),
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

    /**
     * Download Full Database Backup (.sql)
     */
    public function downloadDbBackup()
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('Super Admin') && !$user->can('settings.view') && !$user->can('settings.edit') && !$user->can('settings.read')) {
            abort(403, 'Unauthorized action. You do not have permission to download Database Backup.');
        }

        if (function_exists('set_time_limit')) {
            @set_time_limit(600);
        }
        @ini_set('memory_limit', '512M');
        @ignore_user_abort(true);

        $dbConnection = config('database.default', 'mysql');
        $databaseName = config("database.connections.{$dbConnection}.database") ?: DB::connection()->getDatabaseName();
        $dateStr = date('Y-m-d_H-i-s');
        $filename = "logictech_db_backup_{$databaseName}_{$dateStr}.sql";

        return response()->streamDownload(function () use ($databaseName) {
            $pdo = DB::connection()->getPdo();

            echo "-- ==========================================================\n";
            echo "-- Logic Tech Engineering ERP - Database Backup\n";
            echo "-- Database: {$databaseName}\n";
            echo "-- Generation Date: " . date('Y-m-d H:i:s') . "\n";
            echo "-- Server OS: " . PHP_OS . " | PHP Version: " . PHP_VERSION . "\n";
            echo "-- ==========================================================\n\n";

            echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            echo "SET AUTOCOMMIT = 0;\n";
            echo "START TRANSACTION;\n";
            echo "SET time_zone = \"+00:00\";\n";
            echo "SET NAMES utf8mb4;\n";
            echo "SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;\n";
            echo "SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;\n";
            echo "SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;\n";
            echo "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;\n";
            echo "SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;\n\n";

            // 1. DUMP BASE TABLES
            $baseTables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);

            foreach ($baseTables as $tblRow) {
                $tableName = $tblRow[0];

                echo "-- ---------------------------------------------------------\n";
                echo "-- Table structure for `{$tableName}`\n";
                echo "-- ---------------------------------------------------------\n";
                echo "DROP TABLE IF EXISTS `{$tableName}`;\n";

                $createStmt = $pdo->query("SHOW CREATE TABLE `{$tableName}`")->fetch(PDO::FETCH_ASSOC);
                if (isset($createStmt['Create Table'])) {
                    echo $createStmt['Create Table'] . ";\n\n";
                }

                $offset = 0;
                $limit = 250;
                $hasHeader = false;

                while (true) {
                    $rows = $pdo->query("SELECT * FROM `{$tableName}` LIMIT {$limit} OFFSET {$offset}")->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        break;
                    }

                    if (!$hasHeader) {
                        echo "-- Dumping data for table `{$tableName}`\n";
                        $hasHeader = true;
                    }

                    $columns = array_keys($rows[0]);
                    $escapedColumns = array_map(function ($c) {
                        return "`" . str_replace("`", "``", $c) . "`";
                    }, $columns);
                    $colSql = implode(", ", $escapedColumns);

                    $valRows = [];
                    foreach ($rows as $row) {
                        $escapedVals = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $escapedVals[] = "NULL";
                            } else {
                                $escapedVals[] = $pdo->quote($val);
                            }
                        }
                        $valRows[] = "(" . implode(", ", $escapedVals) . ")";
                    }

                    echo "INSERT INTO `{$tableName}` ({$colSql}) VALUES\n" . implode(",\n", $valRows) . ";\n";

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();

                    $offset += $limit;
                }

                if ($hasHeader) {
                    echo "\n";
                }
            }

            // 2. DUMP VIEWS
            $views = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll(PDO::FETCH_NUM);
            if (!empty($views)) {
                echo "-- ==========================================================\n";
                echo "-- Views\n";
                echo "-- ==========================================================\n\n";

                foreach ($views as $viewRow) {
                    $viewName = $viewRow[0];
                    echo "-- ---------------------------------------------------------\n";
                    echo "-- View structure for `{$viewName}`\n";
                    echo "-- ---------------------------------------------------------\n";
                    echo "DROP VIEW IF EXISTS `{$viewName}`;\n";

                    $createViewStmt = $pdo->query("SHOW CREATE TABLE `{$viewName}`")->fetch(PDO::FETCH_ASSOC);
                    $viewSql = $createViewStmt['Create View'] ?? ($createViewStmt['Create Table'] ?? null);
                    if ($viewSql) {
                        $viewSql = preg_replace('/DEFINER=[^\s]+\s+/', '', $viewSql);
                        echo $viewSql . ";\n\n";
                    }
                }
            }

            echo "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;\n";
            echo "SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;\n";
            echo "SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;\n";
            echo "SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS;\n";
            echo "SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION;\n";
            echo "COMMIT;\n";

            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }, $filename, [
            'Content-Type' => 'application/sql; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
