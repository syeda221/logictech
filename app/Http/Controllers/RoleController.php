<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        // Ensure newly registered permissions exist automatically
        Permission::firstOrCreate(['name' => 'purchase_pos.create']);
        Permission::firstOrCreate(['name' => 'pos.view']);
        foreach (['stock.adjust.view', 'stock.adjust.create', 'stock.adjust.edit', 'stock.adjust.delete', 'material.usage.view', 'material.usage.create', 'material.usage.delete', 'material.usage.report.view', 'purchase.requisitions.view', 'purchase.requisitions.create', 'purchase.requisitions.edit', 'purchase.requisitions.delete', 'grn.view', 'grn.create', 'grn.edit', 'grn.delete'] as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
        }
        foreach ([
            'website-settings.view',
            'website-settings.create',
            'website-settings.edit',
            'website-settings.delete',
            'website-settings.update',
            'website-settings.upload_manage',
            
            // Web Products permissions
            'web_products.view', 'web_products.read',
            'web_products.create', 'web_products.add',
            'web_products.edit', 'web_products.delete',
            
            // Coupons permissions
            'coupons.view', 'coupons.read',
            'coupons.create', 'coupons.add',
            'coupons.edit', 'coupons.delete',
            
            // Web Orders permissions
            'web_orders.view', 'web_orders.read',
            'web_orders.create', 'web_orders.add',
            'web_orders.edit', 'web_orders.delete',

            // General Settings permissions
            'settings.view', 'settings.read',
            'settings.create', 'settings.add',
            'settings.edit', 'settings.delete', 'settings.update',

            // Web Users permissions
            'web_users.view', 'web_users.read',
            'web_users.create', 'web_users.add',
            'web_users.edit', 'web_users.delete'
        ] as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
        }
        
        // Reset permission cache so any newly created permissions are immediately recognized
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        if ($isSuperAdmin) {
            $roles = Role::orderBy('name', "ASC")->get();
            $allPermissions = Permission::orderBy('name')->get();
        } else {
            // Non-Super Admin: Only show roles other than Super Admin
            $roles = Role::where('name', '!=', 'Super Admin')->orderBy('name', "ASC")->get();
            // Hierarchical: Only show permissions that the logged-in user possesses
            $allPermissions = $authUser ? $authUser->getAllPermissions() : collect([]);
        }

        return view('admin_panel.roles.role', compact(['roles', 'allPermissions', 'isSuperAdmin']));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        $editId = $request->edit_id ?? null;
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,'.$request->edit_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Prevent non-super-admin from creating or renaming to 'Super Admin'
        if (!$isSuperAdmin) {
            if (strtolower(trim($request->name)) === 'super admin') {
                return response()->json(['errors' => ['name' => ['You are not authorized to create or rename to Super Admin.']]]);
            }
            if (!empty($editId)) {
                $checkRole = Role::find($editId);
                if ($checkRole && $checkRole->name === 'Super Admin') {
                    return response()->json(['errors' => ['name' => ['You are not authorized to modify the Super Admin role.']]]);
                }
            }
        }

        // Step 3: Save or update logic
        if (!empty($editId)) {
            $role = Role::find($editId);
            $msg = [
                'success' => 'Roles Updated Successfully',
                'reload' => true
            ];
        } else {
            $role = new Role();
            $msg = [
                'success' => 'Roles Created Successfully',
                'redirect' => route('roles.index')
            ];
        }

        $role->name = $request->name;
        $role->save();

        return response()->json($msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $role = Role::findOrFail($id);
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        if (!$isSuperAdmin && $role->name === 'Super Admin') {
            abort(403, 'Unauthorized to delete Super Admin role.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function updatePermissions(Request $request)
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        $role = Role::findOrFail($request->edit_id);

        if (!$isSuperAdmin && $role->name === 'Super Admin') {
            abort(403, 'Unauthorized to modify permissions for Super Admin role.');
        }

        $requestedPermissions = $request->permissions ?? [];

        if ($isSuperAdmin) {
            // Super Admin has full unrestricted control
            $role->syncPermissions($requestedPermissions);
        } else {
            // Hierarchical permission control:
            // Non-super-admin can only grant or revoke permissions that they themselves possess.
            $myAllowedPermissions = $authUser ? $authUser->getAllPermissions()->pluck('name')->toArray() : [];

            // Permissions the target role already has that are outside the manager's scope are preserved untouched
            $existingOtherPermissions = $role->permissions()
                ->whereNotIn('name', $myAllowedPermissions)
                ->pluck('name')
                ->toArray();

            // From requested permissions, only retain what the manager is actually authorized to assign
            $sanitizedRequestedPermissions = array_values(array_intersect($requestedPermissions, $myAllowedPermissions));

            // Combine preserved permissions + manager's updated selections
            $finalPermissions = array_unique(array_merge($existingOtherPermissions, $sanitizedRequestedPermissions));

            $role->syncPermissions($finalPermissions);
        }

        return back()->with('success', 'Role permissions updated successfully!');
    }
}
