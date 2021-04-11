<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }


}
