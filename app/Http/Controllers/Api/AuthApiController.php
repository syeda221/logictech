<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebCustomer;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:web_customers',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Look up existing Customer by email or phone
        $existingCustomer = null;
        if ($request->email) {
            $existingCustomer = Customer::where('email_address', $request->email)->first();
        }
        if (!$existingCustomer && $request->phone) {
            $existingCustomer = Customer::where('mobile', $request->phone)->first();
        }

        if ($existingCustomer) {
            // Link to existing customer
            $customerId = $existingCustomer->customer_id;
            
            // Sync missing email, mobile, and address fields to the ERP customer record
            if (empty($existingCustomer->email_address) && $request->email) {
                $existingCustomer->email_address = $request->email;
            }
            if (empty($existingCustomer->mobile) && $request->phone) {
                $existingCustomer->mobile = $request->phone;
            }
            if (empty($existingCustomer->address) && $request->address) {
                $existingCustomer->address = $request->address;
            }

            // Update source to 'Both' if it was 'Manual' or empty
            if (empty($existingCustomer->source) || $existingCustomer->source === 'Manual') {
                $existingCustomer->source = 'Both';
            }
            $existingCustomer->save();
        } else {
            // Create a new Customer record in ERP
            $customerId = 'CUST-' . str_pad(Customer::max('id') + 1, 4, '0', STR_PAD_LEFT);
            
            Customer::create([
                'customer_id' => $customerId,
                'customer_name' => $request->name,
                'mobile' => $request->phone,
                'email_address' => $request->email,
                'address' => $request->address,
                'opening_balance' => 0, // Explicitly 0 by default, no ledger entry
                'customer_type' => 'Main Customer',
                'status' => 'active',
                'source' => 'Website',
            ]);
        }

        $customer = WebCustomer::create([
            'customer_id' => $customerId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'password' => Hash::make($request->password),
        ]);

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'user' => $customer,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = WebCustomer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $customer,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user('sanctum')->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function user(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user('sanctum')
        ], 200);
    }
}
