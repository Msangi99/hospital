<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulances', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number', 20)->unique();
            $table->string('model', 100)->nullable();
            $table->string('type')->default('BASIC');
            $table->string('status')->default('AVAILABLE');
            $table->string('current_location')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('ambulance_locations', function (Blueprint $table) {
            $table->id();
            $table->string('ambulance_id', 20);
            $table->string('driver_name', 100);
            $table->decimal('current_lat', 10, 8);
            $table->decimal('current_lng', 11, 8);
            $table->boolean('is_available')->default(true);
            $table->timestamp('last_updated')->nullable();

            $table->unique('ambulance_id');
        });

        Schema::create('ambulance_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('driver_name', 100)->nullable();
            $table->decimal('current_lat', 10, 8)->nullable();
            $table->decimal('current_lng', 11, 8)->nullable();
            $table->string('status')->default('AVAILABLE');
            $table->timestamp('last_updated')->nullable();
        });

        Schema::create('ambulance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('request_status')->default('PENDING');
            $table->timestamp('created_at')->nullable();
            $table->foreignId('nearest_ambulance_id')->nullable()->constrained('ambulances')->nullOnDelete();
            $table->decimal('distance_km', 10, 2)->nullable();
        });

        Schema::create('ambulance_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ambulance_id')->nullable()->constrained('ambulances')->nullOnDelete();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paramedic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('shift_status')->default('OFF_SHIFT');
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->text('reason')->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->text('response');
            $table->string('status')->default('PRIVATE');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('emergencies', function (Blueprint $table) {
            $table->id();
            $table->string('emergency_id', 20)->unique();
            $table->string('phone', 15);
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->text('address')->nullable();
            $table->string('status')->default('PENDING');
            $table->text('medical_note')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('device_info')->nullable();
            $table->foreignId('moderator_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('triggered_by')->constrained('users')->cascadeOnDelete();
            $table->string('emergency_type', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('location', 100)->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('type');
            $table->string('location', 255)->nullable();
            $table->foreignId('contact_person_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('login_time')->nullable();
            $table->string('ip_address', 45)->nullable();
        });

        Schema::create('medical_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->text('symptoms_raw')->nullable();
            $table->string('ai_prediction')->nullable();
            $table->unsignedTinyInteger('risk_level')->default(0);
            $table->text('doctor_notes')->nullable();
            $table->text('ai_summary')->nullable();
            $table->string('status')->default('PRIVATE');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('medical_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('staff_type');
            $table->string('specialization');
            $table->string('registration_no', 100);
            $table->string('license_copy');
            $table->string('verification_status')->default('PENDING');
            $table->text('bio')->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('patient_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('attending_staff_id')->constrained('users')->cascadeOnDelete();
            $table->string('ward_name', 100)->nullable();
            $table->text('admission_reason')->nullable();
            $table->timestamp('admission_date')->nullable();
            $table->dateTime('discharge_date')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->string('status')->default('ADMITTED');
        });

        Schema::create('patient_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('blood_group', 5)->nullable();
            $table->string('emergency_contact', 20)->nullable();
            $table->text('medical_history')->nullable();
        });

        Schema::create('patient_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->string('blood_sugar', 20)->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->unsignedInteger('spo2')->nullable();
            $table->decimal('bmi', 4, 2)->nullable();
            $table->timestamp('recorded_at')->nullable();
        });

        Schema::create('security_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('user_role');
            $table->string('action_performed')->nullable();
            $table->string('resource_accessed', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('previous_hash', 64)->nullable();
            $table->string('current_hash', 64)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('symptom_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->text('symptoms_data')->nullable();
            $table->string('ai_prediction')->nullable();
            $table->unsignedInteger('risk_level')->default(1);
            $table->string('status')->default('PENDING');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('video_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('room_id', 100);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_sessions');
        Schema::dropIfExists('symptom_checks');
        Schema::dropIfExists('security_ledger');
        Schema::dropIfExists('patient_vitals');
        Schema::dropIfExists('patient_details');
        Schema::dropIfExists('patient_admissions');
        Schema::dropIfExists('medical_profiles');
        Schema::dropIfExists('medical_ledger');
        Schema::dropIfExists('login_logs');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('emergency_alerts');
        Schema::dropIfExists('emergencies');
        Schema::dropIfExists('chat_logs');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('ambulance_staff');
        Schema::dropIfExists('ambulance_requests');
        Schema::dropIfExists('ambulance_nodes');
        Schema::dropIfExists('ambulance_locations');
        Schema::dropIfExists('ambulances');
    }
};

