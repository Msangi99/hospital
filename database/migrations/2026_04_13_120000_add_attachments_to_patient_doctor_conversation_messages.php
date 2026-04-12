<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_doctor_conversation_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('patient_doctor_conversation_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('body');
                $table->string('attachment_original_name')->nullable();
                $table->string('attachment_mime', 191)->nullable();
                $table->string('attachment_kind', 20)->nullable();
            }
            if (! Schema::hasColumn('patient_doctor_conversation_messages', 'attachment_size')) {
                $table->unsignedBigInteger('attachment_size')->nullable();
            }
        });

        if (Schema::hasColumn('patient_doctor_conversation_messages', 'body')) {
            Schema::table('patient_doctor_conversation_messages', function (Blueprint $table) {
                $table->text('body')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('patient_doctor_conversation_messages', function (Blueprint $table) {
            if (Schema::hasColumn('patient_doctor_conversation_messages', 'attachment_size')) {
                $table->dropColumn('attachment_size');
            }
        });
    }
};
