<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'register', 'forgotPassword', 'resetPassword']]);
    }

    public function forgotPassword(): JsonResponse
    {
        request()->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(request()->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Ссылка на сброс пароля отправлена'])
            : response()->json(['error' => 'Ошибка при отправке'], 400);
    }

    public function resetPassword(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $status = Password::reset(
            request()->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Пароль успешно изменен'])
            : response()->json(['error' => 'Ошибка при сбросе пароля'], 400);
    }

    private function generateUniqueNickname(): string {
        do {
            $nickname = "user_" . rand(100, 999);
        } while (User::where('nickname', $nickname)->exists());

        return $nickname;
    }

    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $nickname = $this->generateUniqueNickname();

        $user = User::create([
            'name' => request()->name,
            'nickname' => $nickname,
            'email' => request()->email,
            'password' => Hash::make(request()->password),
        ]);

        event(new Registered($user));

        return response()->json($user, 200);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
