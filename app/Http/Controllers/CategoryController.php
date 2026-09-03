<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class CategoryController extends Controller
{
    
    public function index()
    {
        // $userId = Auth::id();
      $category = Category::get();
      return  view("admin_panel.category.index",compact('category'));


    }

    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,' . $request->edit_id . ',id',
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

        /**
         * UPDATE CATEGORY
         */
        if ($request->filled('edit_id')) {
            $category = Category::findOrFail($request->edit_id);
            $category->name = $request->name;
            $category->save();

            return response()->json([
                'status'  => 'success',
                'success' => 'Category Updated Successfully',
                'message' => 'Category Updated Successfully',
                'reload'  => true
            ]);
        }

        /**
         * CREATE CATEGORY
         */
        $category = new Category();
        $category->name = $request->name;
        $category->save();

        /**
         * IF REQUEST FROM PRODUCT PAGE
         */
        if ($request->page === 'product_page') {
            return response()->json([
                'status'  => 'success',
                'success' => true,
                'id'      => $category->id,
                'name'    => $category->name,
                'message' => 'Category Created Successfully'
            ]);
        }

        /**
         * NORMAL FLOW
         */
        return response()->json([
            'status'   => 'success',
            'success'  => 'Category Created Successfully',
            'message'  => 'Category Created Successfully',
            'reload'   => true,
            'redirect' => route('Category.home')
        ]);
    }

    public function delete($id)
    {
        $company = Category::find($id);
        if ($company) {
            $company->delete();
            $msg = [
                'status'  => 'success',
                'success' => 'Category Deleted Successfully',
                'message' => 'Category Deleted Successfully',
                'reload'  => route('Category.home'),
            ];
            if (!request()->ajax() && !request()->wantsJson()) {
                return redirect()->route('Category.home')->with('success', 'Category Deleted Successfully');
            }
        } else {
            $msg = [
                'status'  => 'error',
                'error'   => 'Category Not Found',
                'message' => 'Category Not Found'
            ];
            if (!request()->ajax() && !request()->wantsJson()) {
                return redirect()->route('Category.home')->with('error', 'Category Not Found');
            }
        }
        return response()->json($msg);
    }
   
     
}
