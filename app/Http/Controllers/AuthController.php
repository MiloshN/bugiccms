<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'logout']]);
    }


    public function login(Request $request)
    {
        $login_data = Validator::make(
            $request->all(),
            [
                'email' => 'required|',
                'password' => 'required|min:6',
            ]
        );
        if ($login_data->fails()) {
            return response()->json([$login_data->errors()], 400);
        }
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request): ?\Illuminate\Http\JsonResponse
    {
        $register_data = Validator::make(
            $request->all(),
            [
                'nickname' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]
        );

        if ($register_data->fails()) {
            return response()->json([$register_data->errors()], 400);
        }
        try {
            /* ToDo */
            /* Create first register user as admin, init CMS */
            $user = new User();
            $user['nickname'] = $request->nickname;
            $user['email'] = $request->email;
            $user['password'] = bcrypt($request->password);
            $user->save();

        } catch (\Exception $e) {
            return response()->json(['fatal error' => $e], 502);
        }
        return response()->json(["message" => 'User created successfully', 'user' => $user], 201);

    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard();
    }


}
