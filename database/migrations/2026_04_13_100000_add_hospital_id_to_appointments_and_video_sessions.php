<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('hospital_id')
                ->nullable()
                ->after('doctor_id')
                ->constrained('hospitals')
                ->nullOnDelete();
        });

        Schema::table('video_sessions', function (Blueprint $table) {
            $table->foreignId('hospital_id')
                ->nullable()
                ->after('doctor_id')
                ->constrained('hospitals')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hospital_id');
        });

        Schema::table('video_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hospital_id');
        });
    }
};
