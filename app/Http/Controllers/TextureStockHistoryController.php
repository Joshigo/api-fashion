<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TextureStockHistory;
use App\Models\Texture;
use Illuminate\Support\Facades\Validator;


class TextureStockHistoryController extends Controller
{
    public function index()
    {
        $stockHistory = TextureStockHistory::all();
        return response ()->json($stockHistory);
    }

    public function updateTexture(Request $request, $id)
    {

        $validator = Validator::make($request->all(),[
            'amount' => 'required|numeric',
            'texture_id' => 'required|exists:textures,id',
            'total_stock'=> 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=> $validator->errors()],422);
        }

        $texture = Texture::find($request->input('texture_id'));
        if (is_null($texture)) {
            return response()->json(["message" => "Texture not found"], 404);
        }

        $newTotalStock = $texture->total_stock + $request->input('amount');

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
