<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('patient_doctor_conversations')) {
            return;
        }

        Schema::table('patient_doctor_conversations', function (Blueprint $table): void {
            if (! Schema::hasColumn('patient_doctor_conversations', 'title')) {
                $table->string('title', 120)->nullable()->after('hospital_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('patient_doctor_conversations')) {
            return;
        }

        Schema::table('patient_doctor_conversations', function (Blueprint $table): void {
            if (Schema::hasColumn('patient_doctor_conversations', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
