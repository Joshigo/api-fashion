<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $details = OrderDetail::orderBy("id","desc")->paginate(10);
        $details = OrderDetail::all();
        return response()->json($details);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'description' => 'required|string|max:255',
            'price_unit' => 'required|numeric|min:0',
            'piece_type' => 'required|string|max:255',
            'piece_name' => 'required|string|max:255',
            'price_price' => 'required|numeric|min:0',
            'category_id' => 'required|string|max:255',
            'texture_id' => 'required|string|max:255',
            'texture_name' => 'required|string|max:255',
            'texture_provider' => 'required|string|max:255',
            'color_id' => 'required|string|max:255',
            'color_name' => 'required|string|max:255',
            'color_code' => 'required|string|max:255',
            'order_id' => 'required|exists:orders,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $details = OrderDetail::create($request->all());
        return response()->json($details,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detail = OrderDetail::find($id);
        if(is_null($detail)) {
            return response()->json(["message"=> "detail not found"],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            'description' => 'required|string|max:255',
            'price_unit' => 'required|numeric|min:0',
            'piece_type' => 'required|string|max:255',
            'piece_name' => 'required|string|max:255',
            'price_price' => 'required|numeric|min:0',
            'category_id' => 'required|string|max:255',
            'texture_id' => 'required|string|max:255',
            'texture_name' => 'required|string|max:255',
            'texture_provider' => 'required|string|max:255',
            'color_id' => 'required|string|max:255',
            'color_name' => 'required|string|max:255',
            'color_code' => 'required|string|max:255',
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detail = OrderDetail::find($id);
        if(is_null($detail)) {
            return response()->json(['message'=> 'detail not found'],404);
        }

        $detail->update($request->all());
        return response()->json($detail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = OrderDetail::find($id);
        if(is_null($detail)) {
            return response()->json(['message'=> 'detail not found'],404);
        }
        $detail->delete();
        return response()->json(null,204);
    }
}
