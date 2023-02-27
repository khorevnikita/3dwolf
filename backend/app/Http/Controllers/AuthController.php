<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->whereEmail($request->get("email"))
            ->first();

        if (!Hash::check($request->get('password'), $user->password)) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => [
                    'password' => ['Wrong password. Try again.']
                ],
                'a'=>$request->get('password'),
                'b'=>$user->password
            ], 422);
        }

        $token = $user->createToken('login');

        return response()->json([
            'status' => 'success',
            'access_token' => $token->plainTextToken,
        ]);
    }
}
