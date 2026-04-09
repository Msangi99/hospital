<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('address_line')->nullable()->after('location');
            $table->string('city', 120)->nullable()->after('address_line');
            $table->string('country', 120)->nullable()->after('city');
            $table->string('postal_code', 40)->nullable()->after('country');
            $table->string('contact_phone', 50)->nullable()->after('postal_code');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->string('website')->nullable()->after('contact_email');
            $table->string('license_number', 120)->nullable()->after('website');
            $table->boolean('has_emergency_services')->default(false)->after('license_number');
            $table->text('description')->nullable()->after('has_emergency_services');
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn([
                'address_line',
                'city',
                'country',
                'postal_code',
                'contact_phone',
                'contact_email',
                'website',
                'license_number',
                'has_emergency_services',
                'description',
            ]);
        });
    }
};
