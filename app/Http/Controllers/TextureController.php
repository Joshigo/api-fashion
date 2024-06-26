<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Texture;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TextureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Get(
     *     path="/api/textures",
     *     summary="Show all textures with status true",
     *     @OA\Response(
     *         response=200,
     *         description="show all textures.",
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
            $textures = Texture::where('status', true)->with('piece')->get();
        } else {
            $textures = Texture::with('piece')->get();
        }

        return response()->json($textures);
    }

    /**
 * @OA\Post(
 *     path="/api/textures",
 *     summary="Create texture",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     description="Name of the texture"
 *                 ),
 *                 @OA\Property(
 *                      property="status",
 *                      type="string",
 *                      description="status of the texture(set '1' or '2')"
 *                 ),
 *                 @OA\Property(
 *                     property="piece_id",
 *                     type="number",
 *                     description="piece ID of the texture"
 *                 ),
 *                 @OA\Property(
 *                     property="file",
 *                     type="string",
 *                     format="binary",
 *                     description="Image file of the piece. Only jpg, png, jpeg, gif, svg files are allowed.",
 *                     pattern="^.+\.(jpg|png|jpeg|gif|svg)$"
 *                 ),
 *                 @OA\Property(
 *                     property="color_name",
 *                     type="string",
 *                     description="name of the color"
 *                 ),
 *                 @OA\Property(
 *                     property="color_code",
 *                     type="string",
 *                     description="code of the color"
 *                 ),
 *                 @OA\Property(
 *                     property="total_stock",
 *                     type="number",
 *                     description="total stock of the texture"
 *                 ),
 *                 @OA\Property(
 *                     property="cost_meter_texture",
 *                     type="number",
 *                     description="price meter of texture"
 *                 ),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="texture successfully created",
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
            'status' => 'required|boolean',
            'color_name' => 'required|string|max:255',
            'color_code' => 'required|string|max:255',
            'total_stock' => 'required|numeric|min:0',
            'cost_meter_texture' => 'required|numeric|min:0',
            'file' => 'required|file|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'piece_id' => 'required|exists:pieces,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // dd($request);
        $textureStock = $request->total_stock;

        $texture = new Texture($request->all());
        $texture->total_stock = 0;
        $texture->save();

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'texture-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('textures/' . $texture->id, $fileName, 'public');
            $texture->file_path = $filePath;
            $texture->save();
        }

        $textureStockHistoryController = new TextureStockHistoryController();

        $dataToUpdateTexture = [
            'amount' => $textureStock,
            'texture_id' => $texture->id,
        ];

        $response = $textureStockHistoryController->updateTexture(new Request($dataToUpdateTexture));

        if ($response->getStatusCode()!= 200) {
            return response()->json(['message' => 'Failed to update texture stock history'], 500);
        }

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

     /**
 * @OA\Post(
 *     path="/api/textures/{id}",
 *     summary="Update texture",
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
 *                         description="Name of the texture"
 *                     ),
 *                     @OA\Property(
 *                         property="piece_id",
 *                         type="number",
 *                         description="piece ID of the texture"
 *                     ),
 *                     @OA\Property(
 *                         property="status",
 *                         type="string",
 *                         description="Status of the texture(set '1' or '2')"
 *                     ),
 *                     @OA\Property(
 *                         property="file",
 *                         type="string",
 *                         format="binary",
 *                         description="Image file of the piece. Only jpg, png, jpeg, gif, svg files are allowed.",
 *                         pattern="^.+\.(jpg|png|jpeg|gif|svg)$"
 *                     ),
 *                    @OA\Property(
 *                     property="color_name",
 *                     type="string",
 *                     description="name of the color"
 *                 ),
 *                 @OA\Property(
 *                     property="color_code",
 *                     type="string",
 *                     description="code of the color"
 *                 ),
 *                 @OA\Property(
 *                     property="total_stock",
 *                     type="number",
 *                     description="total stock of the texture"
 *                 ),
 *                 @OA\Property(
 *                     property="cost_meter_texture",
 *                     type="number",
 *                     description="price meter of texture"
 *                 ),
 *                 },
 *                 required={"name", "status", "piece_id"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="texture successfully updated",
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
            'status' => 'required|boolean',
            'color_name' => 'required|string|max:255',
            'color_code' => 'required|string|max:255',
            'total_stock' => 'required|numeric|min:0',
            'cost_meter_texture' => 'required|numeric|min:0',
            'file' => 'sometimes|file|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'piece_id' => 'required|exists:pieces,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $texture = Texture::find($id);
        if (is_null($texture)) {
            return response()->json(['message' => 'Texture not found'], 404);
        }

        $filePath = $texture->file_path;

        $updatedTotalStock = $request->total_stock;
        $TextureTotalStock = $texture->total_stock;

        $newStockTexture = $updatedTotalStock - $TextureTotalStock;

        // dd($newStockTexture);
        // dd($request->total_stock);
        // dd($TextureTotalStock);

        if ($request->hasFile('file')) {
            if ($texture->file_path && Storage::disk('public')->exists($texture->file_path)) {
                Storage::disk('public')->delete($texture->file_path);
            }

            $file = $request->file('file');
            $fileName = 'texture-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('textures/' . $texture->id, $fileName, 'public');
        }
        // $texture->total_stock = 0;
        $texture->update(array_merge(
            $request->except(['total_stock', 'file']),
            ['file_path' => $filePath]
        ));

        $textureStockHistoryController = new TextureStockHistoryController();

        $dataToUpdateTexture = [
            'amount' => $newStockTexture,
            'texture_id' => $texture->id,
            'amount_change' => $updatedTotalStock,
        ];

        $response = $textureStockHistoryController->updateTexture(new Request($dataToUpdateTexture));

        if ($response->getStatusCode()!= 200) {
            return response()->json(['message' => 'Failed to update texture stock history'], 500);
        }

        return response()->json($texture);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     /**
 * @OA\Delete(
 *     path="/api/textures/{id}",
 *     summary="Delete texture",
 *     security={ {"bearerAuth":{}} },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="texture successfully deleted",
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
        $texture = Texture::find($id);
        if (is_null($texture)) {
            return response()->json(['message' => 'Texture not found'], 404);
        }

        // Delete the file if it exists
        if ($texture->file_path && Storage::disk('public')->exists($texture->file_path)) {
            Storage::disk('public')->delete($texture->file_path);
        }

        $texture->delete();
        return response()->json(['message' => 'texture deleted sucessfull'], 201);
    }
}
