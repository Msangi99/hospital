<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sos_requests', function (Blueprint $table) {
            $table->foreignId('nearest_hospital_id')
                ->nullable()
                ->constrained('hospitals')
                ->nullOnDelete();
            $table->json('alerted_hospital_ids')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sos_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('nearest_hospital_id');
            $table->dropColumn('alerted_hospital_ids');
        });
    }
};
