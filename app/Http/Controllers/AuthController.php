<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        return redirect()->route('shop.dashboard');
    }

    public function register(Request $request)
    {
        return redirect()->route('shop.dashboard');
    }
}
