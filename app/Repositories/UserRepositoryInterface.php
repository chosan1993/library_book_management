<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function register(array $data);
    public function login(array $credentials);
    public function logout(User $user);
}
