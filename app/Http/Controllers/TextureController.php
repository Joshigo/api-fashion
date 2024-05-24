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
    public function index(Request $request)
    {
        if ($request->has('status')) {
            $textures = Texture::where('status', true)->with('piece')->get();
        } else {
            $textures = Texture::with('piece')->get();
        }
    
        return response()->json($textures);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'piece_id' => 'required|exists:pieces,id',
            'file' => 'required|file|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $texture = new Texture($request->all());
        $texture->save();
    
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'texture-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('textures/' . $texture->id, $fileName, 'public');
            $texture->file_path = $filePath;
            $texture->save();
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'piece_id' => 'required|exists:pieces,id',
            'file' => 'sometimes|file|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $texture = Texture::find($id);
        if (is_null($texture)) {
            return response()->json(['message' => 'Texture not found'], 404);
        }
    
        $filePath = $texture->file_path;
    
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if ($texture->file_path && Storage::disk('public')->exists($texture->file_path)) {
                Storage::disk('public')->delete($texture->file_path);
            }
    
            // Store the new file
            $file = $request->file('file');
            $fileName = 'texture-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('textures/' . $texture->id, $fileName, 'public');
        }
    
        $texture->update(array_merge(
            $request->except('file'),
            ['file_path' => $filePath]
        ));
    
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

        // Delete the file if it exists
        if ($texture->file_path && Storage::disk('public')->exists($texture->file_path)) {
            Storage::disk('public')->delete($texture->file_path);
        }

        $texture->delete();
        return response()->json(null, 204);
    }
}
