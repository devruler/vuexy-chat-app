<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use App\Http\Controllers\Controller;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function storeMsg(Request $request)
    {
        // return Message::create($request->all());
        // dd($request);
        // $chat = Chat::

        $recipient = User::find($request->payload['id']);

        $chat = Chat::updateOrCreate(
            ['id' => $request->payload['id']],
            ['is_pinned' => $request->payload['isPinned']]
        );

        auth()->user()->chats()->syncWithoutDetaching($chat->id);

        $recipient->chats()->syncWithoutDetaching($chat->id);

        return $chat->messages()->create([
            'content' => $request->payload['msg']['textContent'],
            'is_sent' => $request->payload['msg']['isSent'],
            'is_seen' => $request->payload['msg']['isSeen'],
            'user_id' => auth()->id(),
            'chat_id' => $chat->id,
        ]);
    }

    public function getChats()
    {
        $data = auth()->user()->chats()->with('messages')->get();

        $chats = [];

        foreach ($data as $chat) {
            $chats[$chat->id] = [
                'isPinned' => $chat->is_pinned,
                'msg' => $chat->messages->map(function ($item, $key) {
                    return [
                        'isSeen' => $item->is_seen,
                        'isSent' => $item->is_sent,
                        'textContent' => $item->content,
                        'time' => $item->created_at,
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
        // dd(auth()->user()->chats()->withPivot('chat_id')->pluck('chat_id'));
        $authUserActiveChatsId = auth()->user()->chats()->withPivot('chat_id')->pluck('chat_id');
        return (new User())->where('id', '!=', auth()->id())
            ->whereHas('chats', function ($q) use($authUserActiveChatsId) {
                return $q->whereIn('chat_user.chat_id', $authUserActiveChatsId);
            })
            ->with(['chats.messages'])
            ->get();
    }
}
