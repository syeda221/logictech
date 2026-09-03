<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class SubcategoryController extends Controller
{
    
    public function index()
    {
        $category = Category::get();
      $subcategory = Subcategory::with('category')->get();
      return  view("admin_panel.subcategory.index",compact('subcategory','category'));


    }

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|unique:subcategories,name,' . $request->edit_id . ',id',
        'category_id' => 'required',
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
            ->with('catagory_swal_error', $validator->errors()->first());
    }

    // UPDATE
    if ($request->filled('edit_id')) {
        $subcategory = Subcategory::find($request->edit_id);

        if (!$subcategory) {
            return response()->json([
                'status'  => 'error',
                'error'   => 'Subcategory not found',
                'message' => 'Subcategory not found'
            ], 404);
        }

        $message = 'Subcategory Updated Successfully';
    }
    // CREATE
    else {
        $subcategory = new Subcategory();
        $message = 'Subcategory Created Successfully';
    }

    $subcategory->name = $request->name;
    $subcategory->category_id = $request->category_id;
    $subcategory->save();

    // RESPONSE FOR PRODUCT PAGE MODAL
    if ($request->page === 'product_page') {
        return response()->json([
            'status'  => 'success',
            'success' => true,
            'id'      => $subcategory->id,
            'name'    => $subcategory->name,
            'message' => $message
        ]);
    }

    return response()->json([
        'status'  => 'success',
        'success' => $message,
        'message' => $message,
        'reload'  => true
    ]);
}

    public function delete($id)
    {
        $company = Subcategory::find($id);
        if ($company) {
            $company->delete();
            $msg = [
                'status'  => 'success',
                'success' => 'Subcategory Deleted Successfully',
                'message' => 'Subcategory Deleted Successfully',
                'reload'  => route('subcategory.home'),
            ];
            if (!request()->ajax() && !request()->wantsJson()) {
                return redirect()->route('subcategory.home')->with('success', 'Subcategory Deleted Successfully');
            }
        } else {
            $msg = [
                'status'  => 'error',
                'error'   => 'Subcategory Not Found',
                'message' => 'Subcategory Not Found'
            ];
            if (!request()->ajax() && !request()->wantsJson()) {
                return redirect()->route('subcategory.home')->with('error', 'Subcategory Not Found');
            }
        }
        return response()->json($msg);
    }
}
