<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_worker_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('worker_role', 50);
            $table->string('status', 30)->default('ACTIVE');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['hospital_id', 'user_id']);
            $table->index(['hospital_id', 'worker_role']);
            $table->index(['hospital_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_worker_memberships');
    }
};
