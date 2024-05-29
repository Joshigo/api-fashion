<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class OrderDetailController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $orderDetails = OrderDetail::with('orders')->paginate($perPage);
        return response()->json($orderDetails);
    }
    
    public function store(Request $request)
    {
        $user = $this->validateAndCreateOrAssignUser($request->user);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $order = $this->validateAndCreateOrder($request->order, $user->id);
        if ($order instanceof JsonResponse) {
            return $order;
        }

        $orderDetails = $this->validateAndCreateOrderDetails($request->order_details, $order->id);
        if ($orderDetails instanceof JsonResponse) {
            return $orderDetails;
        }

        return response()->json(['order' => $order, 'order_details' => $orderDetails], 201);
    }

    private function validateAndCreateOrAssignUser($userData)
    {
        $validator = Validator::make($userData, [
            'name' => 'required|string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:15',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $userData['email'])->first();

        if (!$user) {
            $password = $userData['password'] ?? Str::random(10);
            $user = User::create([
                'name' => $userData['name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password' => bcrypt($password),
                'phone' => $userData['phone'],
                'country' => $userData['country'],
                'city' => $userData['city'],
            ]);
        }

        return $user;
    }

    private function validateAndCreateOrder($orderData, $userId)
    {
        $validator = Validator::make($orderData, [
            'status' => 'boolean',
            'neck' => 'numeric|min:0',
            'shoulder' => 'numeric|min:0',
            'arm' => 'numeric|min:0',
            'mid_front' => 'numeric|min:0',
            'bicep' => 'numeric|min:0',
            'bust' => 'numeric|min:0',
            'size' => 'numeric|min:0',
            'waist' => 'numeric|min:0',
            'leg' => 'numeric|min:0',
            'hip' => 'numeric|min:0',
            'skirt_length' => 'numeric|min:0',
            'unit_length' => 'required|in:cm,inch',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return Order::create(array_merge($orderData, ['user_id' => $userId]));
    }

    private function validateAndCreateOrderDetails($orderDetailsData, $orderId)
    {
        $validator = Validator::make(['order_details' => $orderDetailsData], [
            'order_details' => 'required|array',
            'order_details.*.description' => 'required|string|max:255',
            'order_details.*.price_unit' => 'required|numeric|min:0',
            'order_details.*.piece_id' => 'required|string|max:255',
            'order_details.*.piece_type' => 'required|string|max:255',
            'order_details.*.piece_name' => 'required|string|max:255',
            'order_details.*.piece_price' => 'required|numeric|min:0',
            'order_details.*.category_id' => 'required|string|max:255',
            'order_details.*.category_name' => 'required|string|max:255',
            'order_details.*.texture_id' => 'required|string|max:255',
            'order_details.*.texture_name' => 'required|string|max:255',
            'order_details.*.color_id' => 'required|string|max:255',
            'order_details.*.color_name' => 'required|string|max:255',
            'order_details.*.color_code' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderDetails = [];
        foreach ($orderDetailsData as $detail) {
            $detail['order_id'] = $orderId;
            $orderDetails[] = OrderDetail::create($detail);
        }

        return $orderDetails;
    }

    public function show($id)
    {
        $orderDetail = OrderDetail::find($id);
        if (is_null($orderDetail)) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }
        return response()->json($orderDetail);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $orderDetail = OrderDetail::find($id);
        if (is_null($orderDetail)) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }

        $orderDetail->delete();
        return response()->json(null, 204);
    }
}
