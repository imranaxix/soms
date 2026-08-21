<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Message;
use App\Notifications\NewChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Resolve and authorize the connection for the authenticated user.
     * Aborts with 403 if user is not a participant or connection is not accepted.
     */
    private function authorizeConnection(int $connectionId): Connection
    {
        $connection = Connection::findOrFail($connectionId);
        $user = auth()->user();

        $isParticipant = $connection->shop_owner_id === $user->id
            || $connection->manufacturer_id === $user->id;

        if (!$isParticipant || $connection->status !== 'accepted') {
            abort(403, 'You are not authorized to access this conversation.');
        }

        return $connection;
    }

    /**
     * Chat inbox — list accepted connections sorted by latest message.
     */
    public function index()
    {
        $user = auth()->user();

        $connections = Connection::with(['shopOwner', 'manufacturer', 'latestMessage'])
            ->where(function ($q) use ($user) {
                $q->where('shop_owner_id', $user->id)
                  ->orWhere('manufacturer_id', $user->id);
            })
            ->where('status', 'accepted')
            ->get()
            ->sortByDesc(function ($c) {
                return optional($c->latestMessage->first())->created_at;
            })
            ->values();

        // Unread counts per connection
        $unreadCounts = [];
        foreach ($connections as $conn) {
            $unreadCounts[$conn->id] = Message::where('connection_id', $conn->id)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        return view('chat.index', compact('connections', 'unreadCounts'));
    }

    /**
     * Show a conversation thread.
     */
    public function show(int $connection)
    {
        $connection = $this->authorizeConnection($connection);
        $user = auth()->user();

        // Load last 50 messages
        $messages = Message::where('connection_id', $connection->id)
            ->with('sender')
            ->orderBy('created_at')
            ->limit(50)
            ->get();

        // Mark incoming messages as read
        Message::where('connection_id', $connection->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Partner
        $partner = $connection->shop_owner_id === $user->id
            ? $connection->manufacturer
            : $connection->shopOwner;

        // Sidebar connections list (same as index)
        $connections = Connection::with(['shopOwner', 'manufacturer', 'latestMessage'])
            ->where(function ($q) use ($user) {
                $q->where('shop_owner_id', $user->id)
                  ->orWhere('manufacturer_id', $user->id);
            })
            ->where('status', 'accepted')
            ->get()
            ->sortByDesc(fn($c) => optional($c->latestMessage->first())->created_at)
            ->values();

        $unreadCounts = [];
        foreach ($connections as $conn) {
            $unreadCounts[$conn->id] = Message::where('connection_id', $conn->id)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        return view('chat.show', compact('connection', 'messages', 'partner', 'connections', 'unreadCounts'));
    }

    /**
     * Send a message.
     */
    public function send(Request $request, int $connection)
    {
        $connection = $this->authorizeConnection($connection);
        $user = auth()->user();

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'connection_id' => $connection->id,
            'sender_id'     => $user->id,
            'body'          => $request->body,
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'id' => $message->id], 201);
        }

        return redirect()->route('chat.show', $connection->id);
    }

    /**
     * Polling endpoint — returns JSON of messages newer than ?after_id
     */
    public function poll(Request $request, int $connection)
    {
        $connection = $this->authorizeConnection($connection);
        $user = auth()->user();

        $afterId = (int) $request->query('after_id', 0);

        $messages = Message::where('connection_id', $connection->id)
            ->where('id', '>', $afterId)
            ->with('sender')
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'body'        => e($m->body),
                'is_mine'     => $m->sender_id === $user->id,
                'sender_name' => $m->sender->business_name ?? $m->sender->name,
                'time'        => $m->created_at->format('g:i A'),
            ]);

        // Mark newly fetched incoming messages as read
        $incomingIds = Message::where('connection_id', $connection->id)
            ->where('id', '>', $afterId)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->pluck('id');

        if ($incomingIds->count()) {
            Message::whereIn('id', $incomingIds)->update(['read_at' => now()]);
        }

        return response()->json(['messages' => $messages]);
    }

    /**
     * Mark all unread messages in a connection as read (called when opening conversation).
     */
    public function markRead(int $connection)
    {
        $connection = $this->authorizeConnection($connection);
        $user = auth()->user();

        Message::where('connection_id', $connection->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
