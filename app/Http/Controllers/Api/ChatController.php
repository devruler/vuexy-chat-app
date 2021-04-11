<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use App\Http\Controllers\Controller;
use App\Message;
use App\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function storeMsg(Request $request){
        return Message::create($request->all());
    }

    public function getChats(){
        return Chat::all();
    }

    public function getContact(){
        return User::all();
    }
}
