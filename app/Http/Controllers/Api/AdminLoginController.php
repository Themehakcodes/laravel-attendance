<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AdminLoginController extends Controller
{
     public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 401,
                'message' => 'Invalid credentials',
                'data'    => (object)[]
            ], 401);
        }

        // Generate token (Laravel Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        // Successful response
        return response()->json([
            'status'  => 200,
            'message' => 'Login successful',
            'data'    => [
                'user'  => $user,
                'token' => $token
            ]
        ], 200);
    }
}
