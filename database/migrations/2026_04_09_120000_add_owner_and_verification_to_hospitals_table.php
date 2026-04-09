<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->foreignId('owner_user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('verification_status')
                ->default('PENDING')
                ->after('status');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->after('verified_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->text('verification_note')->nullable()->after('verified_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_user_id');
            $table->dropConstrainedForeignId('verified_by_user_id');
            $table->dropColumn([
                'verification_status',
                'verified_at',
                'verification_note',
            ]);
        });
    }
};
