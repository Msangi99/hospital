<?php

use App\Models\DoctorNurseCoordinationChat;
use App\Models\HospitalWorkerMembership;
use App\Models\PatientDoctorConversation;
use App\Models\User;
use App\Services\ConversationAccess;
use App\Services\DoctorNurseCoordinationAccess;
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

    if ((int) $user->id === (int) $conversation->patient_id
        || (int) $user->id === (int) $conversation->doctor_id) {
        return true;
    }

    return ConversationAccess::nurseCanAccessHospitalConversation($user, $conversation);
});

Broadcast::channel('doctor-nurse-coordination.{chatId}', function (User $user, string $chatId) {
    $chat = DoctorNurseCoordinationChat::query()->find($chatId);
    if (! $chat) {
        return false;
    }

    return DoctorNurseCoordinationAccess::canParticipate($user, $chat);
});

Broadcast::channel('hospital.{hospitalId}.ambulance', function (User $user, string $hospitalId) {
    if ((string) ($user->role ?? '') !== 'AMBULANCE') {
        return false;
    }

    return HospitalWorkerMembership::query()
        ->where('user_id', $user->id)
        ->where('hospital_id', (int) $hospitalId)
        ->where('worker_role', 'AMBULANCE')
        ->where('status', 'ACTIVE')
        ->exists();
});
