<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function getOrders(Request $request)
    {
        $orders = EcommerceOrder::where('web_customer_id', auth('sanctum')->id())->with('items')->get();

        return response()->json([
            'status' => 'success',
            'orders' => $orders
        ], 200);
    }
}
