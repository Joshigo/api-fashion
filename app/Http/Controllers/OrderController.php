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

     /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Show orders",
     *     @OA\Response(
     *         response=200,
     *         description="show all orders.",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="An error occurred."
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $orders = Order::with('orderDetails')->paginate($perPage);
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
            'user_id' => 'nullable|exists:users,id',
            ]);

        return response()->json('It should only be in order details', 422);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        // $order = Order::find($id);
        // if (is_null($order)) {
        //     return response()->json(['message' => 'Order not found'], 404);
        // }

        // $order->update($request->all());
        // return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

 /**
 * @OA\Delete(
 *     path="/api/orders/{id}",
 *     summary="Delete order",
 *     security={ {"bearerAuth":{}} },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="order successfully deleted",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="An error occurred."
 *     )
 * )
 */
    public function destroy($id)
    {
        $order = Order::find($id);
        if (is_null($order)) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(['message' => 'order deleted sucessfull'], 201);
    }
}
