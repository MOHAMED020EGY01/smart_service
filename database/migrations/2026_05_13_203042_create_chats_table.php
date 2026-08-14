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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->references('id')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('users')->references('id')->cascadeOnDelete();

            $table->string('last_message')->nullable();
            $table->string('User_name')->nullable();
            $table->string('Provider_name')->nullable();

            $table->unique(['user_id', 'provider_id']);
            
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
