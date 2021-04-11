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
    public function storeMsg(Request $request){
        // return Message::create($request->all());
        return Message::create([
            'content' => $request->msg['textContent'],
            'content' => $request->msg['textContent'],
        ]);
    }

    public function getChats(){
        return Auth::user()->chats;
    }

    public function getContacts(){
        return User::where('id', '!=', Auth::id())->get();
    }

    public function getChatContacts(){
        return auth()->user()->chats;
    }

}
