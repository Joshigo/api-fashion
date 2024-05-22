<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Piece;
use Illuminate\Support\Facades\Validator;

class PieceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('status')) {
            $pieces = Piece::where('status', true)->with('category')->get();
        } else {
            $pieces = Piece::with('category')->get();
        }
    
        return response()->json($pieces);
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
            'color' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $piece = Piece::create($request->all());
        return response()->json($piece, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $piece = Piece::find($id);
        if (is_null($piece)) {
            return response()->json(['message' => 'Piece not found'], 404);
        }
        return response()->json($piece);
    }

    public function showWithRelations($id)
    {
        $piece = Piece::with(['category', 'textures.colors'])->find($id);

        if (is_null($piece)) {
            return response()->json(['message' => 'Piece not found'], 404);
        }

        return response()->json($piece);
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
            'color' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $piece = Piece::find($id);
        if (is_null($piece)) {
            return response()->json(['message' => 'Piece not found'], 404);
        }

        $piece->update($request->all());
        return response()->json($piece);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $piece = Piece::find($id);
        if (is_null($piece)) {
            return response()->json(['message' => 'Piece not found'], 404);
        }

        $piece->delete();
        return response()->json(null, 204);
    }
}
