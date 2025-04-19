<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	use HasFactory;

	protected $table = "comments";

	protected $fillable = [
		"user_id",
		"post_id",
		"comment",
		"parent_id"
	];

	public function child()
	{
		return $this->hasMany(Comment::class, 'parent_id', 'id');
	}

	public function author()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
