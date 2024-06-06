<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\TextureStockHistoryController;

class OrderDetailController extends Controller
{

/**
 * @OA\Get(
 *     path="/api/order-details",
 *     summary="Show order details",
 *     @OA\Response(
 *         response=200,
 *         description="show all order details.",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="An error occurred."
 *    )
 *  )
 */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $orderDetails = OrderDetail::with('order')->paginate($perPage);
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
 *                     @OA\Property(property="category_id", type="integer", example=1),
 *                     @OA\Property(property="texture_id", type="integer", example=1),
 *                     @OA\Property(property="piece_id", type="integer", example=1),
 *                     @OA\Property(property="description", type="string", example="Custom shirt"),
 *                     @OA\Property(property="piece_type", type="string", example="shirt"),
 *                     @OA\Property(property="piece_name", type="string", example="Custom Shirt"),
 *                     @OA\Property(property="piece_usage_meter_texture", type="integer", example=2.50),
 *                     @OA\Property(property="piece_price_base", type="integer", example=5.50),
 *                     @OA\Property(property="piece_file", type="string", example="textures/1/texture-DSC_3540.JPG"),
 *                     @OA\Property(property="category_name", type="string", example="Shirts"),
 *                     @OA\Property(property="texture_name", type="string", example="Cotton"),
 *                     @OA\Property(property="texture_cost_meter", type="integer", example=20.50),
 *                     @OA\Property(property="texture_total_stock", type="integer", example=90.50),
 *                     @OA\Property(property="texture_color_name", type="string", example="amarillo"),
 *                     @OA\Property(property="texture_color_code", type="string", example="31238c"),
 *                     @OA\Property(property="texture_file", type="string", example="textures/1/texture-DSC_3540.JPG")
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="order",
 *                 type="object",
 *                 @OA\Property(property="status", type="boolean", example=true),
 *                 @OA\Property(property="neck", type="number", format="float", example=16.0),
 *                 @OA\Property(property="shoulder", type="number", format="float", example=18.0),
 *                 @OA\Property(property="arm", type="number", format="float", example=12.0),
 *                 @OA\Property(property="mid_front", type="number", format="float", example=20.0),
 *                 @OA\Property(property="bicep", type="number", format="float", example=14.0),
 *                 @OA\Property(property="bust", type="number", format="float", example=38.0),
 *                 @OA\Property(property="size", type="number", format="float", example=10.0),
 *                 @OA\Property(property="waist", type="number", format="float", example=32.0),
 *                 @OA\Property(property="leg", type="number", format="float", example=34.0),
 *                 @OA\Property(property="hip", type="number", format="float", example=40.0),
 *                 @OA\Property(property="skirt_length", type="number", format="float", example=24.0),
 *                 @OA\Property(property="unit_length", type="string", example="'cm','inch'"),
 *                 @OA\Property(property="user_id", type="integer", example=1)
 *             ),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Doe"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *                 @OA\Property(property="phone", type="string", example="+1234567890"),
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
        $orderDetails = $request->input('order_details');
        $order = $this->validateAndCreateOrder($request->order, $user->id, $orderDetails);

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

    private function validateAndCreateOrder($orderData, $userId, $orderDetails)
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

    $order = Order::create(array_merge($orderData, ['user_id' => $userId]));

    return $order;
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
            'order_details.*.piece_file' => 'required|string',
            'order_details.*.status' => '|in:Acepted,Pending,Completed',
            'order_details.*.piece_discount' => 'numeric|min:0|max:100',
            'order_details.*.category_name' => 'required|string|max:255',
            'order_details.*.texture_name' => 'required|string|max:255',
            'order_details.*.texture_cost_meter' => 'required|numeric|min:0',
            'order_details.*.texture_total_stock' => 'required|numeric|min:0',
            'order_details.*.texture_color_name' => 'required|string|max:255',
            'order_details.*.texture_color_code' => 'required|string|max:255',
            'order_details.*.texture_file' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $sumTotalPrice = 0;
        $sumTotaltexture = 0;
        $orderDetails = [];
        foreach ($orderDetailsData as $detail) {
            $detail['order_id'] = $orderId;

            $detail['piece_price_total'] = ($detail['texture_cost_meter'] * $detail['piece_usage_meter_texture']) + $detail['piece_price_base'];
            $sumTotalPrice += $detail['piece_price_total'];
            $sumTotaltexture += $detail['piece_usage_meter_texture'];
            $orderDetails[] = OrderDetail::create($detail);
        }

        $order = Order::find($orderId);
        if ($order) {
            $order->total_price = $sumTotalPrice;
            $order->total_texture = $sumTotaltexture;
            $order->save();
        } else {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return $orderDetails;
    }


    /**
     * @OA\Put(
     *     path="/api/{id}/change-status",
     *     summary="Change status order detail",
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="'accepted','completed' "),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category successfully created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="An error occurred."
     *     )
     * )
     */
    public function changeStatusOrder(Request $request, $id)
    {
        $orderDetail = OrderDetail::find($id);
        if (is_null($orderDetail)) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,pending,completed',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Status could be Acepted, Pending or Completed"], 404);
        }

        $orderDetail->status = $request->input('status');
        $orderDetail->save();

        if ($orderDetail->status === 'accepted' || $orderDetail->status === 'completed') {
            $dataToUpdateTexture = [
                'amount' => -$orderDetail->piece_usage_meter_texture,
                'total_stock' => $orderDetail->texture_total_stock, // Asume que texture_total_stock es la propiedad correcta
                'texture_id' => $orderDetail->texture_id,
            ];

            $textureStockHistoryController = new TextureStockHistoryController();

            $response = $textureStockHistoryController->updateTexture(new Request($dataToUpdateTexture));
            if ($response->getStatusCode()!= 200) {
                return response()->json(['message' => 'Failed to update texture stock'], 500);
            }
        }


        return response()->json(['message' => 'Status changed'], 200);
    }

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
    public function show($id)
    {
        $orderDetail = OrderDetail::find($id);
        if (is_null($orderDetail)) {
            return response()->json(['message' => 'Order detail not found'], 404);
        }
        return response()->json($orderDetail);
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
