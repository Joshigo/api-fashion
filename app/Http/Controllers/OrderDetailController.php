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
    /**
     * @OA\Get(
     *     path="/api/order-details/{id}",
     *     summary="Show order details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="show order details.",
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
        $orderDetails = OrderDetail::with('orders')->paginate($perPage);
        return response()->json($orderDetails);
    }

/**
 * @OA\Post(
 *     path="/api/order-details",
 *     summary="Create a new order and order details",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="order_details",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="description", type="string", example="Description 1"),
 *                     @OA\Property(property="price_unit", type="number", format="float", example=10.0),
 *                     @OA\Property(property="piece_id", type="string", example="piece1"),
 *                     @OA\Property(property="piece_type", type="string", example="type1"),
 *                     @OA\Property(property="piece_name", type="string", example="Piece Name 1"),
 *                     @OA\Property(property="piece_price", type="number", format="float", example=100.0),
 *                     @OA\Property(property="category_id", type="string", example="1"),
 *                     @OA\Property(property="category_name", type="string", example="Category Name 1"),
 *                     @OA\Property(property="texture_id", type="string", example="texture1"),
 *                     @OA\Property(property="texture_name", type="string", example="Texture Name 1"),
 *                     @OA\Property(property="texture_provider", type="string", example="Provider 1"),
 *                     @OA\Property(property="color_id", type="string", example="color1"),
 *                     @OA\Property(property="color_name", type="string", example="Color Name 1"),
 *                     @OA\Property(property="color_code", type="string", example="Code1")
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="order",
 *                 type="object",
 *                 @OA\Property(property="neck", type="number", format="float", example=15.5),
 *                 @OA\Property(property="shoulder", type="number", format="float", example=18.0),
 *                 @OA\Property(property="arm", type="number", format="float", example=24.0),
 *                 @OA\Property(property="mid_front", type="number", format="float", example=20.0),
 *                 @OA\Property(property="bicep", type="number", format="float", example=12.5),
 *                 @OA\Property(property="bust", type="number", format="float", example=36.0),
 *                 @OA\Property(property="size", type="number", format="float", example=10.0),
 *                 @OA\Property(property="waist", type="number", format="float", example=30.0),
 *                 @OA\Property(property="leg", type="number", format="float", example=32.0),
 *                 @OA\Property(property="hip", type="number", format="float", example=40.0),
 *                 @OA\Property(property="skirt_length", type="number", format="float", example=24.0),
 *                 @OA\Property(property="unit_length", type="string", example="inch"),
 *                 @OA\Property(property="user_id", type="integer", example=1)
 *             ),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Doe"),
 *                 @OA\Property(property="email", type="string", example="john.doe2@example.com"),
 *                 @OA\Property(property="phone", type="string", example="1234567890"),
 *                 @OA\Property(property="country", type="string", example="USA"),
 *                 @OA\Property(property="city", type="string", example="New York")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Order and order details created successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="order", type="object"),
 *             @OA\Property(property="order_details", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors.",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="An error occurred."
 *     )
 * )
 */
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
            'order_details.*.category_id' => 'required|exists:categories,id',
            'order_details.*.texture_id' => 'required|exists:textures,id',
            'order_details.*.piece_id' => 'required|exists:pieces,id',
            'order_details.*.description' => 'required|string|max:255',
            'order_details.*.piece_type' => 'required|string|max:255',
            'order_details.*.piece_name' => 'required|string|max:255',
            'order_details.*.piece_usage_meter_texture' => 'required|numeric|min:0',
            'order_details.*.piece_price_base' => 'required|numeric|min:0',
            'order_details.*.status' => 'required|in:Acepted,Pending,Completed',
            'order_details.*.piece_price_total' => 'required|numeric|min:0',
            'order_details.*.piece_discount' => 'numeric|min:0|max:100',
            'order_details.*.category_name' => 'required|string|max:255',
            'order_details.*.texture_name' => 'required|string|max:255',
            'order_details.*.texture_cost_meter' => 'required|numeric|min:0',
            'order_details.*.texture_total_stock' => 'required|numeric|min:0',
            'order_details.*.texture_color_name' => 'required|string|max:255',
            'order_details.*.texture_color_code' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderDetails = [];
        foreach ($orderDetailsData as $detail) {
            $detail['order_id'] = $orderId;
            // dd($detail);
            $orderDetails[] = OrderDetail::create($detail);
        }

        return $orderDetails;
    }

    public function changeStatusOrder(Request $request, $id)
    {
        $orderDetail = OrderDetail::find($id);
        if (is_null($orderDetail)) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Acepted,Pending,Completed',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Status could be Acepted, Pending or Completed"], 404);
        }

        $orderDetail->status = $request->input('status');
        $orderDetail->save();
        return response()->json(['message' => 'Status changed'], 200);
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

/**
 * @OA\Delete(
 *     path="/api/order-details/{id}",
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
 *         description="order detail successfully deleted",
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
        $orderDetail = OrderDetail::find($id);
        if (is_null($orderDetail)) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }

        $orderDetail->delete();
        return response()->json(['message' => 'order detail deleted sucessfull'], 201);
    }
}
