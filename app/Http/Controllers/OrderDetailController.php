<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        // Validar y crear o asignar el usuario
        $userValidator = Validator::make($request->user, [
            'name' => 'required|string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:15',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);
    
        if ($userValidator->fails()) {
            return response()->json($userValidator->errors(), 422);
        }
    
        $user = User::where('email', $request->user['email'])->first();
    
        if (!$user) {
            $password = $request->user['password'] ?? Str::random(10);
            $user = new User([
                'name' => $request->user['name'],
                'last_name' => $request->user['last_name'],
                'email' => $request->user['email'],
                'password' => bcrypt($password),
                'phone' => $request->user['phone'],
                'country' => $request->user['country'],
                'city' => $request->user['city'],
            ]);
            $user->save();
        }
    
        // Validar la orden
        $orderValidator = Validator::make($request->order, [
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
            'user_id' => 'required|exists:users,id',
        ]);
    
        if ($orderValidator->fails()) {
            return response()->json($orderValidator->errors(), 422);
        }
    
        // Crear la orden
        $order = Order::create(array_merge($request->order, ['user_id' => $user->id]));
    
        // Validar los detalles de la orden
        $detailsValidator = Validator::make($request->all(), [
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
            'order_details.*.texture_provider' => 'required|string|max:255',
            'order_details.*.color_id' => 'required|string|max:255',
            'order_details.*.color_name' => 'required|string|max:255',
            'order_details.*.color_code' => 'required|string|max:255',
            'order_details.*.order_id' => 'required|exists:orders,id',
        ]);
    
        if ($detailsValidator->fails()) {
            return response()->json($detailsValidator->errors(), 422);
        }
    
        // Crear los detalles de la orden
        $orderDetails = [];
        foreach ($request->order_details as $detail) {
            $detail['order_id'] = $order->id;
            $orderDetails[] = OrderDetail::create($detail);
        }
    
        return response()->json(['order' => $order, 'order_details' => $orderDetails], 201);
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
        // return response()->json({});
        // $validator = Validator::make($request->all(), [
        //     'description' => 'required|string|max:255',
        //     'price_unit' => 'required|numeric|min:0',
        //     'piece_id' => 'required|string|max:255',
        //     'piece_type' => 'required|string|max:255',
        //     'piece_name' => 'required|string|max:255',
        //     'piece_price' => 'required|numeric|min:0',
        //     'category_id' => 'required|string|max:255',
        //     'category_name' => 'required|string|max:255',
        //     'texture_id' => 'required|string|max:255',
        //     'texture_name' => 'required|string|max:255',
        //     'texture_provider' => 'required|string|max:255',
        //     'color_id' => 'required|string|max:255',
        //     'color_name' => 'required|string|max:255',
        //     'color_code' => 'required|string|max:255',
        //     'order_id' => 'required|exists:orders,id',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        // $orderDetail = OrderDetail::find($id);
        // if (is_null($orderDetail)) {
        //     return response()->json(['message' => 'Order detail not found'], 404);
        // }

        // $orderDetail->update($request->all());
        // return response()->json($orderDetail);
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
