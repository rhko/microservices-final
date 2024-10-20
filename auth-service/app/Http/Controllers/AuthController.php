<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request) {
        $user = User::create(
            $request->only('first_name', 'last_name', 'email', 'is_admin') +
            [
                'password' => Hash::make($request->input('password'))
            ]
        );

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function login(Request $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'error' => 'invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        return response()->json(['token' => $user->createToken('token', [$request->input('scope')])->plainTextToken]);
    }

    public function user(Request $request) {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function updateInfo(Request $request)
    {
        $user = $request->user();

        $user->update($request->only('first_name', 'last_name', 'email'));

        return response()->json($user, Response::HTTP_ACCEPTED);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response()->json($user, Response::HTTP_ACCEPTED);
    }

    public function scopeCan(Request $request, $scope) {
        if (!$request->user()->tokenCan($scope)) {
            abort(401, 'unauthorized');
        }

        return true;
    }
}
