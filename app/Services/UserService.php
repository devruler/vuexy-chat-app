<?php

namespace App\Services;

use App\User;

class UserService {
    public static function setUsersStatusOffline(){
        User::all()->each->update([
            'status' => 'offline'
        ]);
    }
}
