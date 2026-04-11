<?php

use App\Models\PatientDoctorConversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('doctor.{doctorId}', function (User $user, string $doctorId) {
    return (string) ($user->role ?? '') === 'MEDICAL_TEAM' && (int) $user->id === (int) $doctorId;
});

Broadcast::channel('conversation.{conversationId}', function (User $user, string $conversationId) {
    $conversation = PatientDoctorConversation::query()->find($conversationId);
    if (! $conversation) {
        return false;
    }

    return (int) $user->id === (int) $conversation->patient_id
        || (int) $user->id === (int) $conversation->doctor_id;
});
