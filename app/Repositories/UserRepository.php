<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function register(array $data)
    {
        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
        ]);
    }

    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return [
                'token'         => $user->createToken('LibraryBookManagement')->plainTextToken,
                'token_type'    => 'Bearer Token'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'The credentials are incorrect.',
            ];
        }
    }

    public function logout(User $user)
    {
        $user->currentAccessToken()->delete();
    }
}
