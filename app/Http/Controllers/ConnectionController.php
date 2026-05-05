<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ConnectionController extends Controller
{
    public function search(Request $request)
    {
        $email = $request->query('email');
        $currentUser = auth()->user();
        
        if (!$email) {
            return response()->json(['error' => 'Email is required.'], 400);
        }

        $targetRole = $currentUser->role === 'shop_owner' ? 'manufacturer' : 'shop_owner';
        
        $foundUser = User::where('email', $email)
                         ->where('role', $targetRole)
                         ->first();
                         
        if (!$foundUser) {
            return response()->json(['error' => 'No user found.'], 404);
        }
        
        return response()->json([
            'id' => $foundUser->id,
            'business_name' => $foundUser->business_name,
            'name' => $foundUser->name,
            'email' => $foundUser->email,
        ]);
    }
}
