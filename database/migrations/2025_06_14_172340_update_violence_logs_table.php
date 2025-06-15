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
        Schema::table('violence_logs', function (Blueprint $table) {
            $table->dropColumn('message_id');
            $table->dropColumn('post_id');
            $table->dropColumn('comment_id');
        });
        Schema::table('violence_logs', function (Blueprint $table) {
            $table->string('message_id')->nullable();
            $table->string('post_id')->nullable();
            $table->string('comment_id')->nullable();
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
