<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        $usersQuery = User::where('email', '!=', 'superadmin@example.com')->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'superadmin');
        });

        if (!$isSuperAdmin) {
            // Non-super-admins cannot see or manage Super Admin users
            $usersQuery->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Super Admin');
            });

            // Hierarchical role filtering: Non-Super Admin can ONLY see/assign roles
            // whose permissions are a subset of $authUser's own granted permissions.
            $myPermissions = $authUser ? $authUser->getAllPermissions()->pluck('name')->toArray() : [];
            $allRoles = Role::with('permissions')->where('name', '!=', 'Super Admin')->get()->filter(function ($role) use ($myPermissions) {
                $rolePerms = $role->permissions->pluck('name')->toArray();
                return empty(array_diff($rolePerms, $myPermissions));
            })->values();
        } else {
            $allRoles = Role::all();
        }

        $users = $usersQuery->get();

        return view('admin_panel.users.users', compact(['users', 'allRoles']));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        $editId = $request->edit_id ?? null;
        $passwordRule = $editId ? 'nullable' : 'required';

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$request->edit_id,
            'password' => $passwordRule,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if (! empty($editId)) {
            $user = User::find($editId);
            if (!$isSuperAdmin && $user->hasRole('Super Admin')) {
                return response()->json(['errors' => ['name' => ['You are not authorized to modify a Super Admin user.']]]);
            }
            $msg = [
                'success' => 'User Updated Successfully',
                'reload' => true,
            ];
        } else {
            $user = new User;
            $msg = [
                'success' => 'User Created Successfully',
                'redirect' => route('users.index'),
            ];
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Hierarchical role filtering for non-super-admins
        $assignedRoles = $request->roles ?? [];
        if (!$isSuperAdmin) {
            $myPermissions = $authUser ? $authUser->getAllPermissions()->pluck('name')->toArray() : [];

            $allowedRoleNames = Role::with('permissions')->where('name', '!=', 'Super Admin')->get()->filter(function ($role) use ($myPermissions) {
                $rolePerms = $role->permissions->pluck('name')->toArray();
                return empty(array_diff($rolePerms, $myPermissions));
            })->pluck('name')->toArray();

            $sanitizedRequestedRoles = array_values(array_intersect($assignedRoles, $allowedRoleNames));

            if (!empty($editId)) {
                $existingRoles = $user->getRoleNames()->toArray();
                $preservedRoles = array_values(array_diff($existingRoles, $allowedRoleNames));
                $assignedRoles = array_unique(array_merge($preservedRoles, $sanitizedRequestedRoles));
            } else {
                $assignedRoles = $sanitizedRequestedRoles;
            }
        }

        // Always sync roles (empty array will remove all roles)
        $user->syncRoles($assignedRoles);

        return response()->json($msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $user = User::findOrFail($id);
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        if (!$isSuperAdmin && $user->hasRole('Super Admin')) {
            abort(403, 'Unauthorized to delete a Super Admin user.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function updateRoles(Request $request)
    {
        $user = User::findOrFail($request->edit_id);
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        if (!$isSuperAdmin && $user->hasRole('Super Admin')) {
            return response()->json(['error' => 'Unauthorized to modify a Super Admin user.'], 403);
        }

        $assignedRoles = $request->roles ?? [];
        if (!$isSuperAdmin) {
            $myPermissions = $authUser ? $authUser->getAllPermissions()->pluck('name')->toArray() : [];

            $allowedRoleNames = Role::with('permissions')->where('name', '!=', 'Super Admin')->get()->filter(function ($role) use ($myPermissions) {
                $rolePerms = $role->permissions->pluck('name')->toArray();
                return empty(array_diff($rolePerms, $myPermissions));
            })->pluck('name')->toArray();

            $sanitizedRequestedRoles = array_values(array_intersect($assignedRoles, $allowedRoleNames));

            $existingRoles = $user->getRoleNames()->toArray();
            $preservedRoles = array_values(array_diff($existingRoles, $allowedRoleNames));
            $assignedRoles = array_unique(array_merge($preservedRoles, $sanitizedRequestedRoles));
        }

        // Assign new roles (by name)
        $user->syncRoles($assignedRoles);

        // Return JSON so AJAX handlers get a clear response
        return response()->json(['success' => 'User roles updated successfully!', 'reload' => true]);
    }
}
