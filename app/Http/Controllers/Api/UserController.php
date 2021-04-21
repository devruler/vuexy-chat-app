<?php

namespace App\Http\Controllers\Api;

use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Events\UserStatusUpdated;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateUserOnline(User $user){

        $user->update([
            'status' => 'online'
        ]);

        broadcast(new UserOnline($user))->toOthers();

        return $user;
    }

    public function updateUserOffline(User $user){

        $user->update([
            'status' => 'offline'
        ]);

        broadcast(new UserOffline($user))->toOthers();

        return $user;
    }

    public function updateUsersStatus(Request $request){
        $ids = collect($request->users)->pluck('id');
        $onlineUsers = User::whereIn('id', $ids)
        ->get()
        ->each
        ->update(['status' => 'online']);

        $offlineUsers = User::whereNotIn('id', $ids)
        ->get()
        ->each
        ->update(['status' => 'offline']);

        return response(['message' => 'users status has been succesfully updated!']);
    }

}
