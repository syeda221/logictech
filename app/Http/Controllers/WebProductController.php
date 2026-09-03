<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class WebProductController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasAnyPermission(['web_products.view', 'web_products.read'])) {
            abort(403, 'Unauthorized action. You do not have permission to view Web Products.');
        }

        $query = Product::query();

        // Optional filtering by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Optional filtering by visibility
        if ($request->has('visibility') && $request->visibility != '') {
            $query->where('is_web_visible', $request->visibility);
        }

        $products = $query->orderBy('id', 'desc')->paginate(50);
        $categories = Category::all();

        return view('admin_panel.web_products.index', compact('products', 'categories'));
    }

    public function updateAjax(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('web_products.edit')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action. You do not have permission to edit Web Products.'], 403);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'field' => 'required|in:is_web_visible,show_on_homepage,promo_tag,web_sale_price',
        ]);

        $product = Product::findOrFail($request->product_id);
        $field = $request->field;
        $value = $request->value;

        $product->$field = $value;
        $product->save();

        return response()->json(['status' => 'success', 'message' => 'Updated successfully.']);
    }

    public function getWebSettings($id)
    {
        if (!auth()->user()->hasAnyPermission(['web_products.view', 'web_products.read'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action. You do not have permission to view Web Product settings.'], 403);
        }

        $product = Product::with('webImages')->findOrFail($id);
        return response()->json(['status' => 'success', 'product' => $product]);
    }

    public function updateWebSettings(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('web_products.edit')) {
            abort(403, 'Unauthorized action. You do not have permission to edit Web Product settings.');
        }

        $product = Product::findOrFail($id);
        
        $product->is_web_visible = $request->has('is_web_visible') ? 1 : 0;
        $product->show_on_homepage = $request->has('show_on_homepage') ? 1 : 0;
        $product->auto_hide_out_of_stock = $request->has('auto_hide_out_of_stock') ? 1 : 0;
        $product->promo_tag = $request->promo_tag;
        $product->web_sale_price = $request->web_sale_price;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->save();

        if ($request->hasFile('web_main_image')) {
            // Delete old main image if exists
            if ($product->web_main_image && file_exists(public_path('uploads/products/' . $product->web_main_image))) {
                unlink(public_path('uploads/products/' . $product->web_main_image));
            }
            $file = $request->file('web_main_image');
            $fileName = time() . '_main_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $fileName);
            $product->web_main_image = $fileName;
            $product->save();
        }

        if ($request->hasFile('web_images')) {
            // Delete old web images
            $oldImages = \App\Models\ProductWebImage::where('product_id', $product->id)->get();
            foreach ($oldImages as $oldImg) {
                if (file_exists(public_path('uploads/products/' . $oldImg->image_path))) {
                    unlink(public_path('uploads/products/' . $oldImg->image_path));
                }
                $oldImg->delete();
            }

            // Save new images
            foreach ($request->file('web_images') as $file) {
                if ($file && $file->isValid()) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/products'), $fileName);

                    \App\Models\ProductWebImage::create([
                        'product_id' => $product->id,
                        'image_path' => $fileName,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Website settings for ' . $product->item_name . ' updated successfully.');
    }
}
