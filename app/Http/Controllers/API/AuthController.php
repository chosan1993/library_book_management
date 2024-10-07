<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepositoryInterface;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Gate;

class AuthController extends BaseController
{   
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
    * Get All Users
    *
    * @return \Illuminate\Http\Response
    */
    public function index() 
    {
        if (Gate::denies('manage-books')) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $user = $this->userRepository->getAllUsers();
        return $this->sendResponse($user, 'All users data successfully.', 200);
    }

    /**
    * Register api
    *
    * @return \Illuminate\Http\Response
    */
    public function register(Request $request)
    {
        $input      = $request->all();
        $validator = Validator::make($input, [
            'name'      => 'required|string',
            'email'     => 'required|string|email|unique:users',
            'password'  => 'required|string|confirmed',
            'role'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }

        $user = $this->userRepository->register($input);
        
        return $this->sendResponse($user, 'User register successfully.', 201);
    }

    /**
    * Login api
    *
    * @return \Illuminate\Http\Response
    */
    public function login(Request $request)
    {
        $input      = $request->all();
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required|string', 
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }

        $credentials = $request->only('email', 'password');

        $response = $this->userRepository->login($credentials);

        if (isset($response['success']) && !$response['success']) {
            return $this->sendError($response['message'], [], 401); 
        }

        return $this->sendResponse($response, 'User login successfully.', 200);
    }

    /**
     * Logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->userRepository->logout($request->user());
        return $this->sendResponse([], 'User logged out successfully.', 200);
    }
}
