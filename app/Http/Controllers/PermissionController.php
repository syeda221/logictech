<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        foreach (['stock.adjust.view', 'stock.adjust.create', 'stock.adjust.edit', 'stock.adjust.delete'] as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
        }

        if ($isSuperAdmin) {
            $permissions = Permission::orderBy('name', "ASC")->get();
        } else {
            $permissions = $authUser ? $authUser->getAllPermissions() : collect([]);
        }

        return view('admin_panel.permissions.permission', compact('permissions'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        if (!$isSuperAdmin) {
            return response()->json(['errors' => ['name' => ['Only Super Admin is authorized to create or modify system permissions.']]]);
        }

        $editId = $request->edit_id ?? null;
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $request->edit_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if (!empty($editId)) {
            $permission = Permission::findOrFail($editId);
            $msg = [
                'success' => 'Permission Updated Successfully',
                'reload' => true
            ];
        } else {
            $permission = new Permission();
            $msg = [
                'success' => 'Permission Created Successfully',
                'redirect' => route('permissions.index')
            ];
        }

        $permission->name = $request->name;
        $permission->save();

        return response()->json($msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('Super Admin') || $authUser->email === 'admin@admin.com');

        if (!$isSuperAdmin) {
            abort(403, 'Only Super Admin is authorized to delete system permissions.');
        }

        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
