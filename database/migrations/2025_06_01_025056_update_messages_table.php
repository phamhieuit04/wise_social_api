<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('messages', function (Blueprint $table) {
			$table->id();
			$table->integer('user_id');
			$table->integer('room_id')->nullable();
			$table->string('message', 255)->nullable();
			$table->integer('is_view')->default(1);
			$table->timestamps();
		});
		Schema::create('chat_rooms', function (Blueprint $table) {
			$table->bigInteger('id');
			$table->text('user_id')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		//
	}
};
