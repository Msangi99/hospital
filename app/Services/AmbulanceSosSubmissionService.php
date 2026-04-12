<?php

namespace App\Services;

use App\Events\AmbulanceSosCreated;
use App\Models\SosRequest;
use App\Support\SafeBroadcast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AmbulanceSosSubmissionService
{
    public function submit(Request $request, string $redirectRouteName): RedirectResponse
    {
        $needsPhone = ! $request->user() || ! $request->user()->phone;

        $data = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'address' => ['nullable', 'string', 'max:5000'],
            'phone' => [
                Rule::requiredIf($needsPhone),
                'nullable',
                'string',
                'max:40',
            ],
        ]);

        $phone = $request->user()?->phone ?? ($data['phone'] ?? null);
        if ($phone === null || trim((string) $phone) === '') {
            return redirect()
                ->route($redirectRouteName)
                ->withErrors(['phone' => __('ambulance.phone_required')])
                ->withInput();
        }

        $sos = SosRequest::query()->create([
            'user_id' => $request->user()?->id,
            'phone' => trim((string) $phone),
            'latitude' => (float) $data['latitude'],
            'longitude' => (float) $data['longitude'],
            'address' => $data['address'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'status' => SosRequest::STATUS_RECEIVED,
        ]);

        $hospitals = app(AmbulanceSosNearestHospitalsService::class)->hospitalsToAlert($sos);
        if ($hospitals !== []) {
            $sos->nearest_hospital_id = $hospitals[0]['id'];
            $sos->alerted_hospital_ids = array_map(static fn (array $h): int => (int) $h['id'], $hospitals);
            $sos->save();

            SafeBroadcast::dispatch(new AmbulanceSosCreated($sos->fresh(), $hospitals));
        }

        return redirect()
            ->route($redirectRouteName)
            ->with('status', __('public.sos_received'));
    }
}
