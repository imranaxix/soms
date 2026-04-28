<?php

namespace App\Http\Controllers;
use App\Models\User;
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
        $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
        ]);

        //Attempt to login
        if(auth()->attempt($credentials, $request->remember)){
            $request->session()->regenerate();
            
            $user = auth()->user();
        
            //Redirect based on the  role
            if ($user->role === 'manufacturer') {
                return redirect()->route('manufacturer.dashboard');
            }

            return redirect()->route('shop.dashboard');
        }

        //If login fails
        return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {

        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'business_name' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:shop_owner,manufacturer'],
        ]);

        
        // Create the user
        $user = User::create($fields);

        // Log the user in immediately after registering
        auth()->login($user);
        
        // Redirect based on role
        return ($user->role === 'manufacturer') 
            ? redirect()->route('manufacturer.dashboard') 
            : redirect()->route('shop.dashboard');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
