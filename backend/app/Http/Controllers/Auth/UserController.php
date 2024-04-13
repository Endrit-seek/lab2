<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function register(){
        return 'register';
    }

    public function login(){
        return 'logintest';
    }

    public function refreshToken(){
        return 'refreshToken';
    }

    public function logout(){
        return 'logout';
    }

    public function forgotPassword(){
        return 'forgotPassword';
    }
}
