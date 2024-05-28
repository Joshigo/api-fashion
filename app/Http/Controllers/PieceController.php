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
     *     summary="Show categories",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todos los usuarios.",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'color' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
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
        return response()->json(null, 204);
    }
}
