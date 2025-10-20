<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    /**
     * Display the chat interface.
     */
    public function index(Request $request): Response
    {
        // Get all users except current user for conversation list
        $users = User::where('id', '!=', Auth::id())
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'unread_count' => Message::where('receiver_id', Auth::id())
                        ->where('sender_id', $user->id)
                        ->unread()
                        ->count(),
                ];
            });

        return Inertia::render('admin/chat/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Get messages between current user and another user.
     */
    public function getMessages(Request $request, User $user)
    {
        $messages = Message::betweenUsers(Auth::id(), $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->toISOString(),
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'avatar' => $message->sender->avatar,
                    ],
                ];
            });

        // Mark messages as read
        Message::where('receiver_id', Auth::id())
            ->where('sender_id', $user->id)
            ->unread()
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    /**
     * Send a message to another user.
     */
    public function sendMessage(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $message->load(['sender', 'receiver']);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'message' => $message->message,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at->toISOString(),
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'avatar' => $message->sender->avatar,
            ],
        ]);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message)
    {
        if ($message->receiver_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count for current user.
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }
}
