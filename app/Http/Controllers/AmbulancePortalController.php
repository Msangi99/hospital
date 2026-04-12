<?php

namespace App\Http\Controllers;

use App\Models\HospitalWorkerMembership;
use App\Models\SosRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AmbulancePortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $hospitalIds = $this->ambulanceHospitalIds($user);

        $openPool = SosRequest::query()
            ->where('status', SosRequest::STATUS_RECEIVED)
            ->whereNull('assigned_user_id')
            ->orderByDesc('id')
            ->limit(120)
            ->get()
            ->filter(fn (SosRequest $sos) => $this->sosVisibleToAmbulanceCrew($sos, $hospitalIds))
            ->take(30)
            ->values();

        $myActive = SosRequest::query()
            ->where('assigned_user_id', $user->id)
            ->whereNotIn('status', [SosRequest::STATUS_COMPLETED, SosRequest::STATUS_CANCELLED])
            ->orderByDesc('id')
            ->get();

        return view('role.ambulance.dashboard', [
            'openPool' => $openPool,
            'myActive' => $myActive,
            'availability' => (string) ($user->ambulance_availability ?? 'AVAILABLE'),
        ]);
    }

    public function run(Request $request, SosRequest $sos): View
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($sos->assigned_user_id === $user->id, 403);

        return view('role.ambulance.run', [
            'sos' => $sos->loadMissing(['requester:id,name,email,phone']),
        ]);
    }

    public function history(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $completed = SosRequest::query()
            ->where('assigned_user_id', $user->id)
            ->whereIn('status', [SosRequest::STATUS_COMPLETED, SosRequest::STATUS_CANCELLED])
            ->orderByDesc('completed_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('role.ambulance.history', [
            'requests' => $completed,
        ]);
    }

    public function claim(Request $request, SosRequest $sos): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $this->sosVisibleToAmbulanceCrew($sos, $this->ambulanceHospitalIds($user))) {
            return redirect()
                ->route('ambulance.portal.dashboard')
                ->with('error', __('roleui.ambulance_claim_not_authorized'));
        }

        if ((string) ($user->ambulance_availability ?? 'AVAILABLE') !== 'AVAILABLE') {
            return redirect()
                ->route('ambulance.portal.dashboard')
                ->with('error', __('roleui.ambulance_must_be_available'));
        }

        $updated = DB::transaction(function () use ($sos, $user) {
            $row = SosRequest::query()->whereKey($sos->id)->lockForUpdate()->first();
            if (! $row) {
                return false;
            }
            if ($row->status !== SosRequest::STATUS_RECEIVED || $row->assigned_user_id !== null) {
                return false;
            }

            $row->assigned_user_id = $user->id;
            $row->status = SosRequest::STATUS_DISPATCHED;
            $row->dispatched_at = now();
            $row->save();

            return true;
        });

        if (! $updated) {
            return redirect()
                ->route('ambulance.portal.dashboard')
                ->with('error', __('roleui.ambulance_claim_failed'));
        }

        return redirect()
            ->route('ambulance.portal.run', $sos)
            ->with('status', __('roleui.ambulance_claim_ok'));
    }

    public function advance(Request $request, SosRequest $sos): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($sos->assigned_user_id === $user->id, 403);

        $map = [
            SosRequest::STATUS_DISPATCHED => SosRequest::STATUS_EN_ROUTE,
            SosRequest::STATUS_EN_ROUTE => SosRequest::STATUS_ON_SCENE,
            SosRequest::STATUS_ON_SCENE => SosRequest::STATUS_TRANSPORTING,
            SosRequest::STATUS_TRANSPORTING => SosRequest::STATUS_COMPLETED,
        ];

        $next = $map[$sos->status] ?? null;
        if ($next === null) {
            return back()->with('error', __('roleui.ambulance_advance_invalid'));
        }

        $sos->status = $next;
        if ($next === SosRequest::STATUS_COMPLETED) {
            $sos->completed_at = now();
        }
        $sos->save();

        if ($next === SosRequest::STATUS_COMPLETED) {
            return redirect()
                ->route('ambulance.portal.dashboard')
                ->with('status', __('roleui.ambulance_run_completed'));
        }

        return back()->with('status', __('roleui.ambulance_status_updated'));
    }

    public function cancelRun(Request $request, SosRequest $sos): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($sos->assigned_user_id === $user->id, 403);
        abort_unless(! $sos->isTerminal(), 403);

        $sos->status = SosRequest::STATUS_CANCELLED;
        $sos->completed_at = now();
        $sos->save();

        return redirect()
            ->route('ambulance.portal.dashboard')
            ->with('status', __('roleui.ambulance_run_cancelled'));
    }

    public function availability(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ambulance_availability' => ['required', 'string', 'in:AVAILABLE,OFF_DUTY'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->ambulance_availability = $data['ambulance_availability'];
        $user->save();

        return back()->with('status', __('roleui.ambulance_availability_saved'));
    }

    /**
     * @return list<int>
     */
    private function ambulanceHospitalIds(User $user): array
    {
        return HospitalWorkerMembership::query()
            ->where('user_id', $user->id)
            ->where('worker_role', 'AMBULANCE')
            ->where('status', 'ACTIVE')
            ->pluck('hospital_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /**
     * @param  list<int>  $crewHospitalIds
     */
    private function sosVisibleToAmbulanceCrew(SosRequest $sos, array $crewHospitalIds): bool
    {
        $alerted = $sos->alerted_hospital_ids;
        if (is_array($alerted) && $alerted !== []) {
            if ($crewHospitalIds === []) {
                return true;
            }

            $alertedInts = array_map(static fn ($id) => (int) $id, $alerted);

            return count(array_intersect($crewHospitalIds, $alertedInts)) > 0;
        }

        return true;
    }
}
