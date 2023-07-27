<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ProfileRequest;
use App\Http\Requests\Auth\SetPasswordRequest;
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
            ], 422);
        }

        $token = $user->createToken('login');

        return response()->json([
            'status' => 'success',
            'access_token' => $token->plainTextToken,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = User::query()
            //->with("permission")
            ->with("customer")
            ->find(auth("sanctum")->id());

        $user->last_activity_date = now();
        $user->save();

        return $this->resourceItemResponse('user', $user);
    }

    public function profile(ProfileRequest $request): JsonResponse
    {
        $user = auth("sanctum")->user();
        $user->fill($request->all());
        $user->save();
        return $this->resourceItemResponse('user', $user);
    }

    public function setPassword(SetPasswordRequest $request): JsonResponse
    {
        $user = auth("sanctum")->user();
        if (!Hash::check($request->get('old_password'), $user->password)) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => [
                    'old_password' => ['Неправильный пароль']
                ],
            ], 422);
        }
        #$user->password = $request->get("password");
        $user->save();

        return $this->emptySuccessResponse();
    }
}
