<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['Ha ocurrido un error' => $validator->errors()], 422);
        }

        $user = new User([
            'name' => $request->input('name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone' => $request->input('phone'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
        ]);

        $user->save();

        return response()->json(['message' => 'Usuario registrado con éxito'], 201);
    }

    public function index()
    {
        $users = User::all();
    }
    public function login(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'msg' => ['*Los datos ingresados son incorrectos'],
                ],
            ],  422);
        }

        $token = $user->createToken($request->email)->plainTextToken;
        return response()->json([
            'res' => true,
            'token' => $token,
            'user' => $user
        ],200);
    }
}
