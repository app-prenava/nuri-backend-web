<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
    // Tambahkan logging untuk debugging
    
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'ibu_hamil',
    ]);
    
    // Verifikasi bahwa user benar-benar dibuat

    $token = Auth::guard('api')->login($user);

    return response()->json([
        'status' => 'success',
        'message' => 'User registered successfully',
        'user' => $user,
        'authorization' => [
            'token' => $token,
            'type' => 'bearer',
        ]
    ]);
}
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => Auth::guard('api')->user(),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            ]
        ]);
    }
    
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::guard('api')->user(),
            'authorization' => [
                'token' => Auth::guard('api')->refresh(),
                'type' => 'bearer',
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            ]
        ]);
    }

    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }
}