<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportChatController extends Controller
{
    private function getAdminId()
    {
        $admin = User::role('admin')->first();
        return $admin ? $admin->id : 1;
    }

  
    public function messages()
    {
        $userId = Auth::id();
        $adminId = $this->getAdminId();
        
        $messages = Message::where(function ($query) use ($userId, $adminId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $adminId);
        })->orWhere(function ($query) use ($userId, $adminId) {
            $query->where('sender_id', $adminId)
                  ->where('receiver_id', $userId);
        })
        ->with('sender:id,name,avatar')
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages from admin as read
        Message::where('sender_id', $adminId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Count unread messages
        $unreadCount = Message::where('sender_id', $adminId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'messages' => $messages,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Send message to admin
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $adminId = $this->getAdminId();

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $adminId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $message->load('sender:id,name,avatar');

        // Broadcast to admin
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message)
    {
        // Only receiver can mark as read
        if ($message->receiver_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $message->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}

