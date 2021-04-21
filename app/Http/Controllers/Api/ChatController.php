<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use App\Events\NewChatMessage;
use App\Events\NewChatStarted;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function storeMsg(Request $request)
    {
        // validate file
        $request->validate([
            'attachment' => 'sometimes|file|mimes:txt,pdf,doc,ppt,xls,docx,pptx,xlsx,rar,zip,jpg,jpeg,png|max:5000'
        ]);

        $payload = json_decode($request->payload);

        // Get recipient user
        $recipient = User::find($payload->id);

        // Get chat if exists
        $chat = Chat::whereIn('recipient_id', [auth()->id(), $payload->id])
        ->whereIn('sender_id', [auth()->id(), $payload->id])
        ->first();

        if ($chat) {
            // Update chat if exists
            $chat->is_pinned = $payload->isPinned;
            $chat->save();
        } else {
            // Else create new chat with $chatId which is the sum of the two chat users ids
            $chat = Chat::create([
                // 'id' => $chatId,
                'recipient_id' => $payload->id,
                'sender_id' => auth()->id(),
                'is_pinned' => $payload->isPinned
            ]);

            // Broadcast new chat started notification
            broadcast(new NewChatStarted($chat))->toOthers();
        }

        // Attach the chat to the appropriate users

        auth()->user()->chats()->syncWithoutDetaching($chat->id);

        $recipient->chats()->syncWithoutDetaching($chat->id);

        // Create and attach the messages to the chat

        $message = $chat->messages()->create([
            'content' => $payload->msg->textContent,
            'is_sent' => $payload->msg->isSent,
            'is_seen' => $payload->msg->isSeen,
            'user_id' => auth()->id(),
            'chat_id' => $chat->id,
        ]);

        if($request->hasFile('attachment')){
            UploadService::upload($request->file('attachment'), $message);
        }

        // Broadcast the new message to other users

        $message->load('chat', 'attachment');

        broadcast(new NewChatMessage($message))->toOthers();

        return $message;
    }

    public function getChats()
    {
        // Get current user active chats
        $currentUserChats = auth()->user()->chats()->with('messages.attachment')->get();

        $chats = [];

        // Manupilate and return the expected data to the frontend
        foreach ($currentUserChats as $chat) {
            $chats[$chat->sender_id !== auth()->id() ? $chat->sender_id : $chat->recipient_id] = [
                'isPinned' => $chat->is_pinned,
                'chat_id' => $chat->id,
                'msg' => $chat->messages->map(function ($msg, $key) {
                    return [
                        'isSeen' => $msg->is_seen,
                        'isSent' => $msg->user_id === auth()->id() ? $msg->is_sent : !$msg->is_sent,
                        'textContent' => $msg->content,
                        'attachment' => $msg->attachment,
                        'time' => $msg->created_at,
                    ];
                })
            ];
        }
        return $chats;
    }

    public function getContacts()
    {
        // Get the contacts of all users except the current user
        return User::where('id', '!=', Auth::id())->get();
    }

    public function getChatContacts()
    {
        // Get the ids of current user active chats
        $authUserActiveChatsId = auth()->user()->chats()->withPivot('chat_id')->pluck('chat_id');

        // Get active chat contacts for the current user
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
        // Retrieve the recipient id
        $chatRecipientId = $request->id;

        // Mark the recipient messages as seen
        return User::where('id', $chatRecipientId)->first()
            ->messages()
            ->update([
                'is_seen' => 1
            ]);
    }

    public function setPinned(Request $request)
    {
        // Get chat id from the sum of the current user id and the contact "recipient" id
        $chatId = (auth()->id() + $request->contactId);

        // Find and set the chat "pinned" value
        return Chat::whereIn('recipient_id', [auth()->id(), $request->contactId])
        ->whereIn('sender_id', [auth()->id(), $request->contactId])
        ->update([
            'is_pinned' => $request->value,
        ]);
    }
}
