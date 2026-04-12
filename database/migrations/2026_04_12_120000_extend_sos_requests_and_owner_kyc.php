<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sos_requests', function (Blueprint $table) {
            $table->string('status', 32)->default('RECEIVED')->after('user_agent');
            $table->foreignId('assigned_user_id')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('dispatched_at')->nullable()->after('assigned_user_id');
            $table->timestamp('completed_at')->nullable()->after('dispatched_at');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->timestamp('kyc_submitted_at')->nullable()->after('verification_note');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('ambulance_availability', 20)->default('AVAILABLE')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('sos_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_user_id');
            $table->dropColumn(['status', 'dispatched_at', 'completed_at']);
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn('kyc_submitted_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ambulance_availability');
        });
    }
};
