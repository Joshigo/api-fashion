<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('orderDetails')->get();
        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
            'neck' => 'required|numeric|min:0',
            'shoulder' => 'required|numeric|min:0',
            'arm' => 'required|numeric|min:0',
            'mid_front' => 'required|numeric|min:0',
            'bicep' => 'required|numeric|min:0',
            'bust' => 'required|numeric|min:0',
            'size' => 'required|numeric|min:0',
            'waist' => 'required|numeric|min:0',
            'leg' => 'required|numeric|min:0',
            'hip' => 'required|numeric|min:0',
            'skirt_length' => 'required|numeric|min:0',
            'unit_length' => 'required|in:cm,inch',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        $order = Order::create(array_merge($request->all(), ['user_id' => $user->id]));

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with('orderDetails')->find($id);
        if (is_null($order)) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
            'neck' => 'required|numeric|min:0',
            'shoulder' => 'required|numeric|min:0',
            'arm' => 'required|numeric|min:0',
            'mid_front' => 'required|numeric|min:0',
            'bicep' => 'required|numeric|min:0',
            'bust' => 'required|numeric|min:0',
            'size' => 'required|numeric|min:0',
            'waist' => 'required|numeric|min:0',
            'leg' => 'required|numeric|min:0',
            'hip' => 'required|numeric|min:0',
            'skirt_length' => 'required|numeric|min:0',
            'unit_length' => 'required|in:cm,inch',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order = Order::find($id);
        if (is_null($order)) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update($request->all());
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if (is_null($order)) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(null, 204);
    }
}
