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
    /**
     * @OA\Get(
     *     path="/api/colors",
     *     summary="Show colors",
     *     @OA\Response(
     *         response=200,
     *         description="show all colors with status true.",
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

/**
 * @OA\Post(
 *     path="/api/colors",
 *     summary="Create color",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             required={"name","code""status","texture_id"},
 *             @OA\Property(property="name", type="string", example="blue"),
 *             @OA\Property(property="code", type="string", example="#ffff"),
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="texture_id", type="number", example="1"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="color successfully created",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="An error occurred."
 *     )
 * )
 */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
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

    /**
 * @OA\Put(
 *     path="/api/colors/{id}",
 *     summary="Update color",
 *     security={ {"bearerAuth":{}} },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","code""status","texture_id"},
 *             @OA\Property(property="name", type="string", example="Updated Name"),
 *             @OA\Property(property="code", type="string", example="Updated code"),
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="texture_id", type="number", example=1),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="color successfully updated",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="An error occurred."
 *     )
 * )
 */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
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

/**
 * @OA\Delete(
 *     path="/api/colors/{id}",
 *     summary="Delete color",
 *     security={ {"bearerAuth":{}} },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="color successfully deleted",
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
        $color = Color::find($id);
        if (is_null($color)) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        $color->delete();
        return response()->json(['message' => 'color deleted sucessfull'], 201);
    }
}
