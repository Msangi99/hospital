<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_nurse_coordination_messages', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('body');
            $table->string('attachment_original_name', 512)->nullable()->after('attachment_path');
            $table->string('attachment_mime', 191)->nullable()->after('attachment_original_name');
            $table->string('attachment_kind', 32)->nullable()->after('attachment_mime');
            $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_kind');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_nurse_coordination_messages', function (Blueprint $table) {
            $table->dropColumn([
                'attachment_path',
                'attachment_original_name',
                'attachment_mime',
                'attachment_kind',
                'attachment_size',
            ]);
        });
    }
};
