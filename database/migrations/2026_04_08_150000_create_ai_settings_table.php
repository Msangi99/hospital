<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('context')->unique();
            $table->string('provider', 50)->default('openai');
            $table->string('model', 120)->default('gpt-4o-mini');
            $table->text('api_key_encrypted')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->text('system_prompt')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};