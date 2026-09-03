<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class BrandController extends Controller
{
     public function index()
    {
        // $userId = Auth::id();
      $Brand = Brand::get();
      return  view("admin_panel.brand.index",compact('Brand'));


    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:brands,name,' . $request->edit_id . ',id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'error'   => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                    'message' => $validator->errors()->first()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', $validator->errors()->first());
        }

        // UPDATE
        if ($request->filled('edit_id')) {
            $brand = Brand::find($request->edit_id);

            if (!$brand) {
                return response()->json([
                    'status'  => 'error',
                    'error'   => 'Brand not found',
                    'message' => 'Brand not found'
                ], 404);
            }

            $message = 'Brand Updated Successfully';
        }
        // CREATE
        else {
            $brand = new Brand();
            $message = 'Brand Created Successfully';
        }

        $brand->name = $request->name;
        $brand->save();

        // PRODUCT PAGE RESPONSE
        if ($request->page === 'product_page') {
            return response()->json([
                'status'  => 'success',
                'success' => true,
                'id'      => $brand->id,
                'name'    => $brand->name,
                'message' => $message
            ]);
        }

        // NORMAL RESPONSE
        return response()->json([
            'status'  => 'success',
            'success' => $message,
            'message' => $message,
            'reload'  => true
        ]);
    }

    public function delete($id)
    {
        $company = Brand::find($id);
        if ($company) {
            $company->delete();
            $msg = [
                'status'  => 'success',
                'success' => 'Brand Deleted Successfully',
                'message' => 'Brand Deleted Successfully',
                'reload'  => route('Brand.home'),
            ];
            if (!request()->ajax() && !request()->wantsJson()) {
                return redirect()->route('Brand.home')->with('success', 'Brand Deleted Successfully');
            }
        } else {
            $msg = [
                'status'  => 'error',
                'error'   => 'Brand Not Found',
                'message' => 'Brand Not Found'
            ];
            if (!request()->ajax() && !request()->wantsJson()) {
                return redirect()->route('Brand.home')->with('error', 'Brand Not Found');
            }
        }
        return response()->json($msg);
    }
}
