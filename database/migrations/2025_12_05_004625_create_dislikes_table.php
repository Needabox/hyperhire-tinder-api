<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreign('user_id', 'dislikes_user_id_foreign')->references('id')->on('users');
            $table->foreignId('target_user_id')->index();
            $table->foreign('target_user_id', 'dislikes_target_user_id_foreign')->references('id')->on('users');
            $table->unique(['user_id', 'target_user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dislikes');
    }
};
