<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    CONST VIEWED = 2;
    const UNVIEW  =  1;

    use HasFactory;

    protected $table = "messages";

    protected $fillable = [
        "user_id", "friend_id", "message", "is_view"
    ];
}
