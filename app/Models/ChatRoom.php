<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
	use HasFactory;

	protected $table = 'chat_rooms';

	protected $fillable = [
		'id',
		'user_id'
	];

	public function messages()
	{
		return $this->hasMany(Message::class, 'user_id', 'id');
	}
}
