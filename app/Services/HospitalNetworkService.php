<?php

namespace App\Services;

use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class HospitalNetworkService
{
    /**
     * @return array<int, int>
     */
    public static function activeHospitalIdsForUser(User $user): array
    {
        return HospitalWorkerMembership::query()
            ->where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->pluck('hospital_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public static function firstSharedActiveHospitalId(User $userA, User $userB): ?int
    {
        $a = self::activeHospitalIdsForUser($userA);
        $b = self::activeHospitalIdsForUser($userB);
        $intersect = array_values(array_intersect($a, $b));

        return isset($intersect[0]) ? (int) $intersect[0] : null;
    }

    /**
     * @param  array<int, User>  $users
     */
    public static function firstSharedActiveHospitalIdAmongUsers(array $users): ?int
    {
        if ($users === []) {
            return null;
        }

        $intersect = null;
        foreach ($users as $user) {
            $ids = self::activeHospitalIdsForUser($user);
            if ($intersect === null) {
                $intersect = $ids;
            } else {
                $intersect = array_values(array_intersect($intersect, $ids));
            }
            if ($intersect === []) {
                return null;
            }
        }

        return (int) $intersect[0];
    }

    /**
     * Active patients linked to at least one of the given hospitals.
     *
     * @param  array<int, int>  $hospitalIds
     * @return Builder<User>
     */
    public static function assignablePatientsAtHospitalIds(array $hospitalIds): Builder
    {
        return User::query()
            ->where('role', 'PATIENT')
            ->where('status', 'ACTIVE')
            ->when($hospitalIds === [], fn (Builder $q) => $q->whereRaw('1 = 0'))
            ->whereHas('hospitalMemberships', function ($q) use ($hospitalIds): void {
                $q->whereIn('hospital_id', $hospitalIds)->where('status', 'ACTIVE');
            })
            ->orderBy('name');
    }

    /**
     * Active patients who share at least one active hospital with the doctor.
     *
     * @return Builder<User>
     */
    public static function assignablePatientsQueryForDoctor(User $doctor): Builder
    {
        return self::assignablePatientsAtHospitalIds(self::activeHospitalIdsForUser($doctor));
    }

    /**
     * Active patients who share at least one active hospital with the nurse.
     *
     * @return Builder<User>
     */
    public static function assignablePatientsQueryForNurse(User $nurse): Builder
    {
        return self::assignablePatientsAtHospitalIds(self::activeHospitalIdsForUser($nurse));
    }

    /**
     * Medical team members at the nurse’s hospitals who may be selected as the attending doctor
     * (excludes nurses: medical profile staff_type NURSE or active membership worker_role NURSE).
     *
     * @return Builder<User>
     */
    public static function assignableAttendingDoctorsQueryForNurse(User $nurse): Builder
    {
        $hospitalIds = self::activeHospitalIdsForUser($nurse);

        return User::query()
            ->where('role', 'MEDICAL_TEAM')
            ->where('status', 'ACTIVE')
            ->where('id', '!=', $nurse->id)
            ->when($hospitalIds === [], fn (Builder $q) => $q->whereRaw('1 = 0'))
            ->whereHas('hospitalMemberships', function ($q) use ($hospitalIds): void {
                $q->whereIn('hospital_id', $hospitalIds)->where('status', 'ACTIVE');
            })
            ->where(function (Builder $q) use ($hospitalIds): void {
                $q->whereDoesntHave('medicalProfile', function (Builder $p): void {
                    $p->where('staff_type', 'NURSE');
                })->whereDoesntHave('hospitalMemberships', function (Builder $m) use ($hospitalIds): void {
                    $m->whereIn('hospital_id', $hospitalIds)
                        ->where('status', 'ACTIVE')
                        ->where('worker_role', 'NURSE');
                });
            })
            ->orderBy('name');
    }

    /**
     * Active nurses (medical profile staff_type NURSE, or hospital worker_role NURSE) sharing a hospital with the doctor.
     *
     * @return Builder<User>
     */
    public static function assignableNursesQueryForDoctor(User $doctor): Builder
    {
        $hospitalIds = self::activeHospitalIdsForUser($doctor);

        return User::query()
            ->where('role', 'MEDICAL_TEAM')
            ->where('status', 'ACTIVE')
            ->where('id', '!=', $doctor->id)
            ->when($hospitalIds === [], fn (Builder $q) => $q->whereRaw('1 = 0'))
            ->whereHas('hospitalMemberships', function ($q) use ($hospitalIds): void {
                $q->whereIn('hospital_id', $hospitalIds)->where('status', 'ACTIVE');
            })
            ->where(function (Builder $q) use ($hospitalIds): void {
                $q->whereHas('medicalProfile', function (Builder $p): void {
                    $p->where('staff_type', 'NURSE');
                })->orWhereHas('hospitalMemberships', function (Builder $m) use ($hospitalIds): void {
                    $m->whereIn('hospital_id', $hospitalIds)
                        ->where('status', 'ACTIVE')
                        ->where('worker_role', 'NURSE');
                });
            })
            ->orderBy('name');
    }

    /**
     * Pick an active medical team member who shares a hospital with the patient.
     */
    public static function assignableDoctorForPatientVideo(User $patient): ?User
    {
        $hospitalIds = self::activeHospitalIdsForUser($patient);
        if ($hospitalIds === []) {
            return null;
        }

        return User::query()
            ->where('role', 'MEDICAL_TEAM')
            ->where('status', 'ACTIVE')
            ->whereHas('hospitalMemberships', function ($q) use ($hospitalIds): void {
                $q->whereIn('hospital_id', $hospitalIds)->where('status', 'ACTIVE');
            })
            ->inRandomOrder()
            ->first();
    }

    /**
     * Hospitals that have at least one active MEDICAL_TEAM worker (linkable care network).
     *
     * @return Builder<Hospital>
     */
    public static function hospitalsWithBrowseableMedicalTeam(): Builder
    {
        return Hospital::query()
            ->whereHas('workerMemberships', function ($q): void {
                $q->where('worker_role', 'MEDICAL_TEAM')
                    ->where('status', 'ACTIVE')
                    ->whereHas('user', function ($uq): void {
                        $uq->where('role', 'MEDICAL_TEAM')->where('status', 'ACTIVE');
                    });
            })
            ->withCount(['workerMemberships as active_medical_team_count' => function ($q): void {
                $q->where('worker_role', 'MEDICAL_TEAM')
                    ->where('status', 'ACTIVE')
                    ->whereHas('user', function ($uq): void {
                        $uq->where('role', 'MEDICAL_TEAM')->where('status', 'ACTIVE');
                    });
            }])
            ->orderBy('name');
    }

    public static function hospitalHasActiveMedicalTeamLink(int $hospitalId): bool
    {
        return HospitalWorkerMembership::query()
            ->where('hospital_id', $hospitalId)
            ->where('worker_role', 'MEDICAL_TEAM')
            ->where('status', 'ACTIVE')
            ->whereHas('user', function ($q): void {
                $q->where('role', 'MEDICAL_TEAM')->where('status', 'ACTIVE');
            })
            ->exists();
    }
}
