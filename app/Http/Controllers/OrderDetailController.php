<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Validator;

class OrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $details = OrderDetail::orderBy("id","desc")->paginate(10);
        $orderDetails = OrderDetail::with('orders')->get();
        return response()->json($orderDetails);
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
            'description' => 'required|string|max:255',
            'price_unit' => 'required|numeric|min:0',
            'piece_id' => 'required|string|max:255',
            'piece_type' => 'required|string|max:255',
            'piece_name' => 'required|string|max:255',
            'piece_price' => 'required|numeric|min:0',
            'category_id' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'texture_id' => 'required|string|max:255',
            'texture_name' => 'required|string|max:255',
            'texture_provider' => 'required|string|max:255',
            'color_id' => 'required|string|max:255',
            'color_name' => 'required|string|max:255',
            'color_code' => 'required|string|max:255',
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderDetail = OrderDetail::create($request->all());
        return response()->json($orderDetail, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderDetail = OrderDetail::find($id);
        if (is_null($orderDetail)) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }
        return response()->json($orderDetail);
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
            'description' => 'required|string|max:255',
            'price_unit' => 'required|numeric|min:0',
            'piece_id' => 'required|string|max:255',
            'piece_type' => 'required|string|max:255',
            'piece_name' => 'required|string|max:255',
            'piece_price' => 'required|numeric|min:0',
            'category_id' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'texture_id' => 'required|string|max:255',
            'texture_name' => 'required|string|max:255',
            'texture_provider' => 'required|string|max:255',
            'color_id' => 'required|string|max:255',
            'color_name' => 'required|string|max:255',
            'color_code' => 'required|string|max:255',
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderDetail = OrderDetail::find($id);
        if (is_null($orderDetail)) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }

        $orderDetail->update($request->all());
        return response()->json($orderDetail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
