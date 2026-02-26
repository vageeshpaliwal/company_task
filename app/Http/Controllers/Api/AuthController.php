<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $key='login-attempts:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.',
            ], 429);
        }
        if(Auth::attempt($request->only('email', 'password'))){
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'message' => 'User logged in successfully',
                'token_type' => 'Bearer',
            ], 200);
        }
        RateLimiter::hit($key, 60);
        return response()->json([
            'message' => 'Invalid email or password',
        ], 401);
        if($user->status !== 'active'){
            return response()->json([
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }
        RateLimiter::clear($key);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,   
            'message' => 'User logged in successfully',
            'token_type' => 'Bearer',
        ], 200);
    }

    public function signup(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status' => 'active',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'message' => 'User registered successfully',
            'token_type' => 'Bearer',
        ], 201);
    }
}
