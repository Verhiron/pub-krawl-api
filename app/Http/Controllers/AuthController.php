<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    CONST TOKEN_EXPIRY_TIME = 24;

    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $data["data"]["created_at"] = Carbon::now()->format("Y-m-d H:i:s");
        $data["data"]["updated_at"] = Carbon::now()->format("Y-m-d H:i:s");

        $user = User::create($data);

        $expiryDate = Carbon::now()->addHours(self::TOKEN_EXPIRY_TIME);

        $token = $user->createToken('authToken', ['*'], $expiryDate)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);

    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Incorrect Details'
            ], 401);
        }

        $expiryDate = Carbon::now()->addHours(self::TOKEN_EXPIRY_TIME);

        $token = $user->createToken('authToken', ['*'], $expiryDate)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }


    public function session(Request $request){

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated.'
            ], 401);
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout Successful']);
    }

}
