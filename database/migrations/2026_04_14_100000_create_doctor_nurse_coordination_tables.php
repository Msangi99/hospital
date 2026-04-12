<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_nurse_coordination_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('patient_context', 255);
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->nullOnDelete();
            $table->timestamps();

            $table->index(['doctor_id', 'updated_at']);
            $table->index(['nurse_id', 'updated_at']);
        });

        Schema::create('doctor_nurse_coordination_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coordination_chat_id')->constrained('doctor_nurse_coordination_chats')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_nurse_coordination_messages');
        Schema::dropIfExists('doctor_nurse_coordination_chats');
    }
};
