<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);


        if ($validator->fails()) {
            return response([
                'success' => false,
                'errors'=>$validator->errors()->all()
            ], 422);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);

        $token = $user->createToken('Token Name')->accessToken;

        return response([
            'success' => true,
            'is_admin' => false,
            'user_id' => $user->id,
            'access_token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response([
                'success' => false,
                'errors'=>$validator->errors()->all()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('Token Name')->accessToken;

        return response([
            'success' => true,
            'user_id' => $user->id,
            'is_admin' => $user->is_admin,
            'access_token' => $token
        ]);
    }

    public function logout(Request $request)
    {

        $request->user()->token()->revoke();

        return response(['message' => 'Successfully logged out']);
    }
}
