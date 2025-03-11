<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function verifyEmail(int $id, string $hash): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Неверный токен'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email уже подтвержден']);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email успешно подтвержден']);
    }
}
