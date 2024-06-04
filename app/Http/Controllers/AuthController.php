<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{

    // public function registro(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'last_name' => 'string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //         'phone' => 'string|max:15',
    //         'country' => 'string|max:255',
    //         'city' => 'string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['Ha ocurrido un error' => $validator->errors()], 422);
    //     }

    //     $user = new User([
    //         'name' => $request->input('name'),
    //         'last_name' => $request->input('last_name'),
    //         'email' => $request->input('email'),
    //         'password' => bcrypt($request->input('password')),
    //         'phone' => $request->input('phone'),
    //         'country' => $request->input('country'),
    //         'city' => $request->input('city'),
    //     ]);

    //     $user->save();

    //     return response()->json(['message' => 'Usuario registrado con éxito'], 201);
    // }

/**
     * @OA\Post(
     *     path="/api/register-admin",
     *     summary="Create Super admin",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name","email", "password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="short"),
     *             @OA\Property(property="email", type="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *             @OA\Property(property="password_confirmation", type="string", example="12345678"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Super admin successfully created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="An error occurred."
     *     )
     * )
     */

    public function registro_admin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'string|max:15',
            'country' => 'string|max:255',
            'city' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['An error occurred.' => $validator->errors()], 422);
        }

        $user = new User([
            'name' => $request->input('name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone' => $request->input('phone'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'role' => 'admin',
        ]);

        $user->save();

        return response()->json(['message' => 'user successfully registered'], 201);
    }

/**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user - destroy token",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="user-token successfully destroyed",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="An error occurred."
     *     )
     * )
*/
    public function logout(Request $request){

        $user = Auth::user();

        if ($user) {

            $user->currentAccessToken()->delete();


            Auth::guard('web')->logout();

            return response()->json([
                'res' => true,
                'msg' => 'logout sucessfully'
            ], 200);
        }


        return response()->json([
            'res' => false,
            'msg' => 'No authenticated user'
        ], 401);
    }


    public function index()
    {
        $users = User::all();
    }

/**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="user successfully logged",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="An error occurred."
     *     )
     * )
*/
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        $specialAdminPassword = env('SPECIAL_ADMIN_PASSWORD');

        // Verificar si la contraseña es la cadena especial
        if ($request->input('password') == $specialAdminPassword) {
            $user = User::where('email', $request->input('email'))->first();

            if ($user) {
                $token = $user->createToken('Personal Access Token')->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'user' => $user
                ], 200);
            } else {
                return response()->json(['error' => 'Unauthorized, invalid credentials'], 401);
            }
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorized, invalid credentials'], 401);
        }
    }

/**
 * @OA\Get(
 *     path="/api/login-out",
 *     summary="login-out - json response",
 *     @OA\Response(
 *         response=200,
 *         description="sucessfully - Unauthorized, loginout...",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="An error occurred."
 *     )
 * )
 */
    public function login2 (Request $request){
        return response()->json(['error' => 'Unauthorized, loginout...'], 201);
    }

/**
     * @OA\Post(
     *     path="/api/change-password",
     *     summary="change password user",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"user_id", "password", "password_confirmation"},
     *             @OA\Property(property="user_id", type="number", example="1"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *             @OA\Property(property="password_confirmation", type="string", example="12345678"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="user successfully logged",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="An error occurred."
     *     )
     * )
*/

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::find($request->input('user_id'));
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

}
