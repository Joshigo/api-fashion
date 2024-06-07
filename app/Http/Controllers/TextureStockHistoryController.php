<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TextureStockHistory;
use App\Models\Texture;
use Illuminate\Support\Facades\Validator;


class TextureStockHistoryController extends Controller
{

/**
 * @OA\Get(
 *     path="/api/textures-history",
 *     summary="Show texture stock history",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="show all history.",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="An error occurred."
 *    )
 *  )
 */
    public function index()
    {
        $stockHistory = TextureStockHistory::all();
        return response ()->json($stockHistory);
    }


/**
 * @OA\Post(
 *     path="/api/texture-update",
 *     summary="update texture stock",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             required={"name","status"},
 *             @OA\Property(property="amount", type="integer", example=10),
 *             @OA\Property(property="texture_id", type="integer", example=1),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Stock texture sucessfully updated",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="An error occurred."
 *     )
 * )
 */
    public function updateTexture(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'amount' => 'required|numeric',
            // 'amount_change' => 'required|numeric',
            'texture_id' => 'required|exists:textures,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->errors()],422);
        }

        $texture = Texture::find($request->input('texture_id'));
        if (is_null($texture)) {
            return response()->json(["message" => "Texture not found"], 404);
        }

        $newTotalStock = $texture->total_stock + $request->input('amount');

        if ($newTotalStock < 0 ) {
            return response()->json(["message" => "Stock not be negative"], 404);
        }

        $texture->total_stock = $newTotalStock;
        $texture->save();

        $history = new TextureStockHistory();
        $history->texture_id = $texture->id;
        $history->amount = $request->input('amount');
        $history->total_stock = $newTotalStock;
        $history->save();

        return response()->json(['message' => 'Texture stock updated and history recorded successfully'], 200);
    }
}
