<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->query('query');

        $users = User::where('nickname', 'like', "%{$query}%")->limit(5)->get(['id', 'name', 'nickname']);

        return response()->json(['data' => $users]);
    }
}
