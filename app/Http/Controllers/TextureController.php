<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Texture;
use Illuminate\Support\Facades\Validator;

class TextureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $textures = Texture::where('status', true)->with('piece')->get();
        return response()->json($textures);
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
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'piece_id' => 'required|exists:pieces,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $texture = Texture::create($request->all());
        return response()->json($texture, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $texture = Texture::find($id);
        if (is_null($texture)) {
            return response()->json(['message' => 'Texture not found'], 404);
        }
        return response()->json($texture);
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
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'piece_id' => 'required|exists:pieces,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $texture = Texture::find($id);
        if (is_null($texture)) {
            return response()->json(['message' => 'Texture not found'], 404);
        }

        $texture->update($request->all());
        return response()->json($texture);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $texture = Texture::find($id);
        if (is_null($texture)) {
            return response()->json(['message' => 'Texture not found'], 404);
        }

        $texture->delete();
        return response()->json(null, 204);
    }
}
