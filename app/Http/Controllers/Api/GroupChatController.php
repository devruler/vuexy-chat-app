<?php

namespace App\Http\Controllers\Api;

use App\Events\NewGroupChatCreated;
use App\Events\NewGroupChatMessage;
use App\GroupChat;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    public function getChatGroups()
    {
        // Get active chat groups for the current user
        return auth()->user()->chatGroups()->with(['messages.attachment'])->get();
    }

    public function getGroupChatMessages(GroupChat $groupChat)
    {
        // Get active chat groups for the current user
        return $groupChat->load('messages.attachment');
    }

    public function storeGroup(Request $request)
    {
        // Create new group chat
        $group = GroupChat::create([
            'name' => $request->name,
            'is_pinned' => 0,
            'admin_id' => auth()->id(),
        ]);

        // Assign users to group chat
        $group->users()->syncWithoutDetaching([auth()->id(), ...collect($request->contacts)->pluck('id')]);

        // Broadcast new created group
        broadcast(new NewGroupChatCreated($group))->toOthers();

        return auth()->user()->chatGroups()->with(['messages'])->get();
    }

    public function storeGroupChatMsg(Request $request){
        // validation
        $request->payload = json_decode($request->payload);

        $request->validate([
            'payload.id' => ['nullable', 'int'],
            'payload.group_chat_id' => ['nullable', 'int'],
            'payload.isPinned' => ['boolean'],
            'payload.msg.textContent' => ['nullable', 'string',],
            'payload.msg.isSeen' => ['boolean',],
            'payload.msg.isSent' => ['boolean',],
            'attachment' => ['sometimes','file','mimes:txt,pdf,doc,ppt,xls,docx,pptx,xlsx,rar,zip,jpg,jpeg,png,gif','max:5000'],
        ]);

        $payload = $request->payload;

        // Get group chat
        $groupChat = GroupChat::where('id', $payload->group_chat_id)->first();

        // Update group Chat if exists
        $groupChat->is_pinned = $payload->isPinned;
        $groupChat->save();

        // Create and attach the message to the group chat
        $message = $groupChat->messages()->create([
            'content' => $payload->msg->textContent,
            'is_sent' => $payload->msg->isSent,
            'is_seen' => $payload->msg->isSeen,
            'user_id' => auth()->id(),
        ]);

        // upload  and attach file to message if exists
        if($request->hasFile('attachment')){
            UploadService::upload($request->file('attachment'), $message);

        }

        // lazy load attachment relationship
        $message->load('attachment');

        // Broadcast the new message to other users
        broadcast(new NewGroupChatMessage($message))->toOthers();

        return $message;
    }
}
