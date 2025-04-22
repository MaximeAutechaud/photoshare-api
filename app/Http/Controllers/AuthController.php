<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function signup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validated) {
            // create new user
            // return user connecté
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'token' => $user->createToken(time())->plainTextToken,
            ]);
        }
        // throw error
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function authenticate(Request $request): JsonResponse
    {
        $user = User::query()->where('email', $request->get('email'))->first();
        if (Hash::check($request->get('password'), $user->password)) {
            return response()->json([
                'token' => $user->createToken(time())->plainTextToken,
                'user' => $user
            ]);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = User::query()->where('id', $request->params['user_id'])->first();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'status' => 'User disconnected',
            ]);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function dashboard(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'OK',
        ]);
    }
}
