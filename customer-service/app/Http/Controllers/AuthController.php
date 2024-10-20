<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Rhko\UserBridge\UserService;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->only('first_name', 'last_name', 'email', 'password') + ['is_admin' => 0];

        $user = $this->userService->post('register', $data);

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $data = $request->only('email', 'password') + ['scope' => 'customer'];

        $response = $this->userService->post('login', $data);

        $cookie = cookie('jwt', $response['jwt'], 60 * 24);

        return response()->json([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function user(Request $request)
    {
        $user = $this->userService->get('user');
        $orders = Order::where('user_id', $user['id'])->get();
        $user['revenue'] = $orders->sum(fn(Order $order) => $order->total);
        return response()->json($user);
    }

    public function logout()
    {
        $cookie = \Cookie::forget('jwt');

        $this->userService->post('logout');
        return response()->json([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        $user = $this->userService->put('users/info', $request->only('first_name', 'last_name', 'email'));

        return response()->json($user, Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {

        $user = $this->userService->put('users/info', $request->only('password'));

        return response()->json($user, Response::HTTP_ACCEPTED);
    }
}
