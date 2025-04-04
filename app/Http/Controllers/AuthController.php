<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function authenticate(Request $request): JsonResponse
    {
        $user = User::query()->where('email', $request->get('email'))->first();
        if (Hash::check($request->get('password'), $user->password)) {
            return response()->json([
                'token' => $user->createToken(time())->plainTextToken,
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
