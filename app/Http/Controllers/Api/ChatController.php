<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function storeMsg(Request $request)
    {
        $recipient = User::find($request->payload['id']);

        $chat = Chat::where('id', ($request->payload['id'] + auth()->id()))->first();

        if ($chat) {
            $chat->is_pinned = $request->payload['isPinned'];
            $chat->save();
        } else {
            $chat = Chat::create([
                'id' => ($request->payload['id'] + auth()->id()),
                'recipient_id' => $request->payload['id'],
                'is_pinned' => $request->payload['isPinned']
            ]);
        }

        auth()->user()->chats()->syncWithoutDetaching($chat->id);

        $recipient->chats()->syncWithoutDetaching($chat->id);

        $message = $chat->messages()->create([
            'content' => $request->payload['msg']['textContent'],
            'is_sent' => $request->payload['msg']['isSent'],
            'is_seen' => $request->payload['msg']['isSeen'],
            'user_id' => auth()->id(),
            'chat_id' => $chat->id,
        ]);

        broadcast(new NewChatMessage($message))->toOthers();

        return $message;
    }

    public function getChats()
    {
        $currentUserChats = auth()->user()->chats()->with('messages')->get();

        $chats = [];

        foreach ($currentUserChats as $chat) {
            $chats[$chat->recipient_id === auth()->id() ? ($chat->id - auth()->id()) : $chat->recipient_id] = [
                'isPinned' => $chat->is_pinned,
                'chat_id' => $chat->id,
                'msg' => $chat->messages->map(function ($msg, $key) {
                    return [
                        'isSeen' => $msg->is_seen,
                        'isSent' => $msg->user_id === auth()->id() ? $msg->is_sent : !$msg->is_sent,
                        'textContent' => $msg->content,
                        'time' => $msg->created_at,
                    ];
                })
            ];
        }
        return $chats;
    }

    public function getContacts()
    {
        return User::where('id', '!=', Auth::id())->get();
    }

    public function getChatContacts()
    {
        $authUserActiveChatsId = auth()->user()->chats()->withPivot('chat_id')->pluck('chat_id');

        $chatContacts = (new User())->where('id', '!=', auth()->id())
            ->whereHas('chats', function ($q) use ($authUserActiveChatsId) {
                return $q->whereIn('chat_user.chat_id', $authUserActiveChatsId);
            })
            ->with(['chats.messages'])
            ->get();

        return $chatContacts;
    }

    public function markAllSeen(Request $request)
    {
        $chatRecipientId = $request->id;

        return User::where('id', $chatRecipientId)->first()
            ->messages()
            ->update([
                'is_seen' => 1
            ]);
    }
}
