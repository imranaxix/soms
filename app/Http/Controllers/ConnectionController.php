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
            return back()->with('error', 'Email is required.');
        }

        $targetRole = $currentUser->role === 'shop_owner' ? 'manufacturer' : 'shop_owner';
        
        $foundUser = User::where('email', $email)
                         ->where('role', $targetRole)
                         ->first();
                         
        if (!$foundUser) {
            return back()->with('error', 'No user found with this email.');
        }
        
        return back()->with('searchUser', $foundUser);
    }
    public function showProfile($id)
    {
        $user = User::findOrFail($id);
        
        $currentUser = auth()->user();
        if ($user->id === $currentUser->id || $user->role === $currentUser->role) {
            abort(404);
        }

        if ($currentUser->role === 'shop_owner') {
            $user->load(['products.variants']);
            return view('shop-owner.profile.show', compact('user'));
        } else {
            return view('manufacturer.profile.show', compact('user'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id'
        ]);

        $currentUser = auth()->user();
        $targetUser = User::findOrFail($request->target_user_id);

        if ($currentUser->id === $targetUser->id || $currentUser->role === $targetUser->role) {
            return back()->with('error', 'Invalid connection request.');
        }

        $shopOwnerId = $currentUser->role === 'shop_owner' ? $currentUser->id : $targetUser->id;
        $manufacturerId = $currentUser->role === 'manufacturer' ? $currentUser->id : $targetUser->id;

        // Check if connection already exists
        $existing = \App\Models\Connection::where('shop_owner_id', $shopOwnerId)
                        ->where('manufacturer_id', $manufacturerId)
                        ->first();

        if ($existing) {
            if ($existing->status === 'rejected') {
                $existing->update([
                    'status' => 'pending',
                    'initiated_by' => $currentUser->id
                ]);
            } else {
                return back()->with('error', 'A connection or request already exists.');
            }
        } else {
            // Create connection
            \App\Models\Connection::create([
                'shop_owner_id' => $shopOwnerId,
                'manufacturer_id' => $manufacturerId,
                'initiated_by' => $currentUser->id,
                'status' => 'pending'
            ]);
        }

        // Notify target user
        $targetUser->notify(new \App\Notifications\ConnectionRequestReceived($currentUser));

        return back()->with('success', 'Connection request sent successfully!');
    }

    public function accept($id)
    {
        $connection = \App\Models\Connection::findOrFail($id);
        
        // Ensure the current user is part of this connection
        if ($connection->shop_owner_id !== auth()->id() && $connection->manufacturer_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Ensure the current user is the recipient of the request
        if ($connection->initiated_by === auth()->id()) {
            return back()->with('error', 'You cannot accept your own request.');
        }

        $connection->update(['status' => 'accepted']);

        // Mark related notifications as read
        auth()->user()->unreadNotifications->where('type', \App\Notifications\ConnectionRequestReceived::class)->markAsRead();

        return back()->with('success', 'Connection request accepted!');
    }

    public function reject($id)
    {
        $connection = \App\Models\Connection::findOrFail($id);
        
        // Ensure the current user is part of this connection
        if ($connection->shop_owner_id !== auth()->id() && $connection->manufacturer_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($connection->initiated_by === auth()->id()) {
            return back()->with('error', 'You cannot reject your own request.');
        }

        $connection->update(['status' => 'rejected']);
        
        auth()->user()->unreadNotifications->where('type', \App\Notifications\ConnectionRequestReceived::class)->markAsRead();

        return back()->with('success', 'Connection request rejected.');
    }

    public function destroy($id)
    {
        $connection = \App\Models\Connection::findOrFail($id);
        
        // Ensure the current user is part of this connection
        if ($connection->shop_owner_id !== auth()->id() && $connection->manufacturer_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $connection->delete();

        return back()->with('success', 'Connection removed successfully.');
    }
}
