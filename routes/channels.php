<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chat_id}', function ($user, $chat_id) {
    return $user;
});

Broadcast::channel('chats', function ($user) {
    return $user;
});

Broadcast::channel('group-chat.{group_id}', function ($user, $group_id) {
    return $user;
});

Broadcast::channel('group-chats', function ($user) {
    return $user;
});

Broadcast::channel('user-status', function ($user) {
    return $user;
});
