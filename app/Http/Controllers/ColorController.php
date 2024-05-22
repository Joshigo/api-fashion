<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('status')) {
            $colors = Color::where('status', true)->with('texture')->get();
        } else {
            $colors = Color::with('texture')->get();
        }
    
        return response()->json($colors);
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
            'color_name' => 'required|string|max:255',
            'color_code' => 'required|string|max:255',
            'status' => 'required|boolean',
            'texture_id' => 'required|exists:textures,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $color = Color::create($request->all());
        return response()->json($color, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $color = Color::find($id);
        if (is_null($color)) {
            return response()->json(['message' => 'Color not found'], 404);
        }
        return response()->json($color);
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
            'color_name' => 'required|string|max:255',
            'color_code' => 'required|string|max:255',
            'status' => 'required|boolean',
            'texture_id' => 'required|exists:textures,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $color = Color::find($id);
        if (is_null($color)) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        $color->update($request->all());
        return response()->json($color);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $color = Color::find($id);
        if (is_null($color)) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        $color->delete();
        return response()->json(null, 204);
    }
}
