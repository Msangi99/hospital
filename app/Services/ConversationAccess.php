<?php

namespace App\Services;

use App\Models\HospitalWorkerMembership;
use App\Models\MedicalProfile;
use App\Models\PatientDoctorConversation;
use App\Models\User;

class ConversationAccess
{
    public static function isStaffNurse(User $user): bool
    {
        if ((string) ($user->role ?? '') !== 'MEDICAL_TEAM') {
            return false;
        }

        $profile = MedicalProfile::query()->where('user_id', $user->id)->first();
        if ($profile !== null && (string) $profile->staff_type === 'NURSE') {
            return true;
        }

        return HospitalWorkerMembership::query()
            ->where('user_id', $user->id)
            ->where('worker_role', 'NURSE')
            ->where('status', 'ACTIVE')
            ->exists();
    }

    public static function nurseCanAccessHospitalConversation(User $user, PatientDoctorConversation $conversation): bool
    {
        if (! self::isStaffNurse($user)) {
            return false;
        }

        $hospitalId = (int) ($conversation->hospital_id ?? 0);
        if ($hospitalId === 0) {
            return false;
        }

        return HospitalWorkerMembership::query()
            ->where('user_id', $user->id)
            ->where('hospital_id', $hospitalId)
            ->where('status', 'ACTIVE')
            ->exists();
    }

    public static function canParticipate(User $user, PatientDoctorConversation $conversation): bool
    {
        if ((int) $user->id === (int) $conversation->patient_id) {
            return true;
        }

        if ((int) $user->id === (int) $conversation->doctor_id) {
            return true;
        }

        return self::nurseCanAccessHospitalConversation($user, $conversation);
    }
}
