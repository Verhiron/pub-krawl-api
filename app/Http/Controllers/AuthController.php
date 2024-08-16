<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $dateTime = new DateTime();

        $data["data"]["created_at"] = $dateTime->format("Y-m-d H:i:s");
        $data["data"]["updated_at"] = $dateTime->format("Y-m-d H:i:s");

        $user = User::create($data);

        $token = $user->createToken('authToken')->plainTextToken;

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
                'message' => 'Unauthorized'
            ], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }


    public function session(Request $request){
        return response()->json([
            'user' => $request->user(),
        ]);
    }

}
