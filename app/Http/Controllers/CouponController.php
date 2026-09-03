<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasAnyPermission(['coupons.view', 'coupons.read'])) {
            abort(403, 'Unauthorized action. You do not have permission to view Coupons.');
        }

        $coupons = Coupon::latest()->get();
        return view('admin_panel.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasAnyPermission(['coupons.create', 'coupons.add'])) {
            abort(403, 'Unauthorized action. You do not have permission to create Coupons.');
        }

        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        Coupon::create([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend,
            'max_uses' => $request->max_uses,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Coupon created successfully.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        if (!auth()->user()->hasPermissionTo('coupons.edit')) {
            abort(403, 'Unauthorized action. You do not have permission to edit Coupons.');
        }

        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $coupon->update([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend,
            'max_uses' => $request->max_uses,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        if (!auth()->user()->hasPermissionTo('coupons.delete')) {
            abort(403, 'Unauthorized action. You do not have permission to delete Coupons.');
        }

        $coupon->delete();
        return redirect()->back()->with('success', 'Coupon deleted successfully.');
    }
}
