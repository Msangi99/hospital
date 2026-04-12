<?php

namespace App\Services;

use App\Models\DoctorNurseCoordinationChat;
use App\Models\User;

class DoctorNurseCoordinationAccess
{
    public static function canParticipate(User $user, DoctorNurseCoordinationChat $chat): bool
    {
        return (int) $user->id === (int) $chat->doctor_id
            || (int) $user->id === (int) $chat->nurse_id;
    }
}
