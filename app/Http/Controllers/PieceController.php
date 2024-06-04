<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Piece;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PieceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     /**
     * @OA\Get(
     *     path="/api/pieces",
     *     summary="Show pieces",
     *     @OA\Response(
     *         response=200,
     *         description="show all pieces.",
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
            $pieces = Piece::where('status', true)->with('category')->get();
        } else {
            $pieces = Piece::with('category')->get();
        }

        foreach ($pieces as $piece) {
            $piece->price_total = $piece->calculatePriceTotal();
        }

        return response()->json($pieces);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
/**
 * @OA\Post(
 *     path="/api/pieces",
 *     summary="Create piece",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     description="Name of the piece"
 *                 ),
 *                 @OA\Property(
 *                     property="type",
 *                     type="string",
 *                     description="Type of the piece"
 *                 ),
 *                 @OA\Property(
 *                      property="status",
 *                      type="string",
 *                      description="status of the piece(set '1' or '2')"
 *                 ),
 *                 @OA\Property(
 *                     property="price_base",
 *                     type="number",
 *                     description="Price base of the piece"
 *                 ),
 *                 @OA\Property(
 *                     property="usage_meter_texture",
 *                     type="number",
 *                     description="usage meter texture of the piece"
 *                 ),
 *                 @OA\Property(
 *                     property="category_id",
 *                     type="number",
 *                     description="Category ID of the piece"
 *                 ),
 *                 @OA\Property(
 *                     property="file",
 *                     type="string",
 *                     format="binary",
 *                     description="Image file of the piece"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Piece successfully created",
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
            'type' => 'required|string|max:255',
            'status' => 'required|boolean',
            'price_base' => 'required|numeric|min:0',
            'usage_meter_texture' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|file|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $piece = new Piece($request->all());
        $piece->save();

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'piece-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pieces/' . $piece->id, $fileName, 'public');
            $piece->file_path = $filePath;
            $piece->save();
        }

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
/**
 * @OA\Post(
 *     path="/api/pieces/{id}",
 *     summary="Update piece",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 properties={
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         description="Name of the piece"
 *                     ),
 *                     @OA\Property(
 *                         property="type",
 *                         type="string",
 *                         description="Type of the piece"
 *                     ),
 *                     @OA\Property(
 *                         property="status",
 *                         type="string",
 *                         description="Status of the piece"
 *                     ),
 *                     @OA\Property(
 *                         property="price_base",
 *                         type="number",
 *                         description="Price base of the piece"
 *                     ),
 *                     @OA\Property(
 *                         property="usage_meter_texture",
 *                         type="number",
 *                         description="usage meter texture base of the piece"
 *                     ),
 *                     @OA\Property(
 *                         property="category_id",
 *                         type="number",
 *                         description="Category ID of the piece"
 *                     ),
 *                     @OA\Property(
 *                         property="file",
 *                         type="string",
 *                         format="binary",
 *                         description="Image file of the piece. Only jpg, png, jpeg, gif, svg files are allowed.",
 *                         pattern="^.+\.(jpg|png|jpeg|gif|svg)$"
 *                     )
 *                 },
 *                 required={"name", "type", "status", "price_base", "usage_meter_texture", "category_id"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Piece successfully updated",
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
            'type' => 'required|string|max:255',
            'status' => 'required|boolean',
            'price_base' => 'required|numeric|min:0',
            'usage_meter_texture' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'file' => 'sometimes|file|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $piece = Piece::find($id);
        if (is_null($piece)) {
            return response()->json(['message' => 'Piece not found'], 404);
        }

        $filePath = $piece->file_path;

        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if ($piece->file_path && Storage::disk('public')->exists($piece->file_path)) {
                Storage::disk('public')->delete($piece->file_path);
            }

            // Store the new file
            $file = $request->file('file');
            $fileName = 'piece-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pieces/' . $piece->id, $fileName, 'public');
        }

        $piece->update(array_merge(
            $request->except('file'),
            ['file_path' => $filePath]
        ));

        return response()->json($piece);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

 /**
 * @OA\Delete(
 *     path="/api/pieces/{id}",
 *     summary="Delete piece",
 *     security={ {"bearerAuth":{}} },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="piece successfully deleted",
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
        $piece = Piece::find($id);
        if (is_null($piece)) {
            return response()->json(['message' => 'Piece not found'], 404);
        }

        // Delete the file if it exists
        if ($piece->file_path && Storage::disk('public')->exists($piece->file_path)) {
            Storage::disk('public')->delete($piece->file_path);
        }

        $piece->delete();
        return response()->json(['message' => 'Category deleted sucessfull'], 201);
    }
}
