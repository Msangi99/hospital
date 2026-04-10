<?php

namespace App\Http\Controllers;

use App\Models\AiSetting;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\MedicalProfile;
use App\Models\NewsletterSubscriber;
use App\Models\SafeGirlSymptom;
use App\Models\SosRequest;
use App\Models\User;
use App\Models\VideoSession;
use App\Services\AiProviderModelsListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RolePagesController extends Controller
{
    public function hospitalOwnerDashboard(): View
    {
        /** @var User $user */
        $user = request()->user();
        $hospital = Hospital::query()->where('owner_user_id', $user->id)->first();

        return view('role.owner.dashboard', [
            'hospital' => $hospital,
        ]);
    }

    public function hospitalOwnerSection(string $section): View
    {
        $allowed = [
            'workers',
            'departments',
            'services',
            'schedules',
            'reports',
            'billing',
            'settings',
        ];

        abort_unless(in_array($section, $allowed, true), 404);

        return view('role.owner.section', [
            'active' => $section,
            'title' => __('roleui.owner_sidebar_'.$section),
            'description' => __('roleui.owner_section_'.$section.'_desc'),
        ]);
    }

    public function hospitalOwnerWorkers(): View
    {
        /** @var User $user */
        $user = request()->user();
        $hospital = Hospital::query()->where('owner_user_id', $user->id)->first();

        abort_if(! $hospital, 404);

        $workers = HospitalWorkerMembership::query()
            ->with(['user:id,name,email,phone,status,created_at'])
            ->where('hospital_id', $hospital->id)
            ->latest('id')
            ->get();

        return view('role.owner.workers', [
            'hospital' => $hospital,
            'workers' => $workers,
            'workerRoles' => ['MEDICAL_TEAM', 'PATIENT', 'FACILITY', 'AMBULANCE'],
            'workerStatuses' => ['ACTIVE', 'PENDING', 'SUSPENDED'],
        ]);
    }

    public function hospitalOwnerWorkersStore(Request $request): RedirectResponse
    {
        /** @var User $owner */
        $owner = $request->user();
        $hospital = Hospital::query()->where('owner_user_id', $owner->id)->firstOrFail();

        $roles = ['MEDICAL_TEAM', 'PATIENT', 'FACILITY', 'AMBULANCE'];
        $statuses = ['ACTIVE', 'PENDING', 'SUSPENDED'];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'worker_role' => ['required', 'string', 'in:'.implode(',', $roles)],
            'status' => ['required', 'string', 'in:'.implode(',', $statuses)],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'full_name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['worker_role'],
            'status' => $data['status'],
            'password' => Hash::make($data['password']),
        ]);

        HospitalWorkerMembership::query()->create([
            'hospital_id' => $hospital->id,
            'user_id' => $user->id,
            'worker_role' => $data['worker_role'],
            'status' => $data['status'],
            'joined_at' => now(),
        ]);

        return redirect()
            ->route('owner.workers')
            ->with('status', __('roleui.owner_worker_created_success'));
    }

    public function hospitalOwnerProfileUpdate(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $hospital = Hospital::query()->where('owner_user_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'type' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'has_emergency_services' => ['nullable', 'boolean'],
        ]);

        $hospital->fill([
            'name' => $data['name'],
            'location' => $data['location'],
            'address_line' => $data['address_line'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'type' => $data['type'],
            'contact_phone' => $data['phone'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'website' => $data['website'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'description' => $data['description'] ?? null,
            'has_emergency_services' => $request->boolean('has_emergency_services'),
        ])->save();

        if (array_key_exists('phone', $data)) {
            $user->phone = $data['phone'];
            $user->save();
        }

        return redirect()
            ->route('owner.dashboard')
            ->with('status', __('roleui.owner_profile_saved'));
    }

    public function adminDashboard(): View
    {
        return view('role.admin.dashboard');
    }

    public function adminEmergencies(): View
    {
        return view('role.admin.emergencies');
    }

    public function adminNewsletter(): View
    {
        return view('role.admin.newsletter');
    }

    public function adminAlerts(): View
    {
        return view('role.admin.alerts');
    }

    public function adminAiSettings(): View
    {
        $setting = AiSetting::query()->firstOrCreate(
            ['context' => 'safe_girl'],
            ['provider' => 'openai', 'model' => 'gpt-4o-mini', 'is_enabled' => false]
        );

        return view('role.admin.ai-settings', [
            'setting' => $setting,
            'providers' => [
                'openai',
                'gemini',
                'groq',
                'deepseek',
                'mistral',
                'openrouter',
                'ollama',
                'xai',
            ],
        ]);
    }

    public function adminAiSettingsUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:120'],
            'api_key' => ['nullable', 'string', 'max:5000'],
            'is_enabled' => ['nullable', 'boolean'],
            'system_prompt' => ['nullable', 'string', 'max:20000'],
        ]);

        $setting = AiSetting::query()->firstOrCreate(
            ['context' => 'safe_girl'],
            ['provider' => 'openai', 'model' => 'gpt-4o-mini', 'is_enabled' => false]
        );

        $payload = [
            'provider' => (string) $data['provider'],
            'model' => (string) $data['model'],
            'is_enabled' => $request->boolean('is_enabled'),
            'system_prompt' => ($data['system_prompt'] ?? null) ?: null,
        ];

        $apiKeyInput = trim((string) ($data['api_key'] ?? ''));
        if ($apiKeyInput !== '') {
            $payload['api_key_encrypted'] = Crypt::encryptString($apiKeyInput);
        }

        $setting->fill($payload)->save();

        return back()->with('status', __('roleui.ai_settings_saved'));
    }

    public function adminAiModelsFetch(Request $request, AiProviderModelsListService $models): JsonResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'max:50'],
            'api_key' => ['nullable', 'string', 'max:5000'],
            'use_saved_key' => ['nullable', 'boolean'],
        ]);

        $provider = strtolower((string) $data['provider']);
        $apiKey = trim((string) ($data['api_key'] ?? ''));

        if ($apiKey === '' && $request->boolean('use_saved_key')) {
            $setting = AiSetting::query()->where('context', 'safe_girl')->first();
            if ($setting?->api_key_encrypted) {
                try {
                    $apiKey = Crypt::decryptString((string) $setting->api_key_encrypted);
                } catch (\Throwable) {
                    $apiKey = '';
                }
            }
        }

        if ($apiKey === '' && $provider !== 'ollama') {
            return response()->json(['message' => __('roleui.ai_models_key_required')], 422);
        }

        $allowed = ['openai', 'gemini', 'groq', 'deepseek', 'mistral', 'openrouter', 'ollama', 'xai'];
        if (! in_array($provider, $allowed, true)) {
            return response()->json(['message' => __('roleui.ai_models_invalid_provider')], 422);
        }

        try {
            $list = $models->list($provider, $apiKey);

            return response()->json(['models' => $list]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => __('roleui.ai_models_fetch_failed'),
            ], 502);
        }
    }

    public function adminUsers(): View
    {
        $users = User::query()
            ->select(['id', 'name', 'full_name', 'email', 'phone', 'role', 'status', 'created_at'])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total' => User::query()->count(),
            'superadmin' => User::query()->where('role', 'SUPERADMIN')->count(),
            'hospital_owner' => User::query()->where('role', 'HOSPITAL_OWNER')->count(),
            'medical_team' => User::query()->where('role', 'MEDICAL_TEAM')->count(),
            'patient' => User::query()->where('role', 'PATIENT')->count(),
            'facility' => User::query()->where('role', 'FACILITY')->count(),
            'ambulance' => User::query()->where('role', 'AMBULANCE')->count(),
        ];

        return view('role.admin.users', [
            'users' => $users,
            'stats' => $stats,
            'roles' => ['SUPERADMIN', 'HOSPITAL_OWNER', 'MEDICAL_TEAM', 'PATIENT', 'FACILITY', 'AMBULANCE'],
            'statuses' => ['ACTIVE', 'PENDING', 'SUSPENDED'],
        ]);
    }

    public function adminUsersStore(Request $request): RedirectResponse
    {
        $roles = ['SUPERADMIN', 'HOSPITAL_OWNER', 'MEDICAL_TEAM', 'PATIENT', 'FACILITY', 'AMBULANCE'];
        $statuses = ['ACTIVE', 'PENDING', 'SUSPENDED'];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', 'in:'.implode(',', $roles)],
            'status' => ['nullable', 'string', 'in:'.implode(',', $statuses)],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'full_name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'status' => $data['status'] ?? 'ACTIVE',
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('admin.users')
            ->with('status', __('roleui.user_created_success'));
    }

    public function adminFacilities(): View
    {
        $facilities = Hospital::query()
            ->with(['owner:id,name,email,status'])
            ->select([
                'id',
                'owner_user_id',
                'name',
                'location',
                'address_line',
                'city',
                'country',
                'postal_code',
                'type',
                'status',
                'verification_status',
                'verification_note',
                'verified_at',
                'contact_phone',
                'contact_email',
                'website',
                'license_number',
                'has_emergency_services',
                'description',
                'latitude',
                'longitude',
                'created_at',
            ])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total' => Hospital::query()->count(),
            'pending_verification' => Hospital::query()->where('verification_status', 'PENDING')->count(),
            'approved' => Hospital::query()->where('verification_status', 'APPROVED')->count(),
            'suspended' => Hospital::query()->where('verification_status', 'SUSPENDED')->count(),
            'owners' => User::query()->where('role', 'HOSPITAL_OWNER')->count(),
        ];

        return view('role.admin.facilities', [
            'facilities' => $facilities,
            'stats' => $stats,
            'statuses' => ['Online', 'Offline'],
            'verificationStatuses' => ['PENDING', 'APPROVED', 'REJECTED', 'SUSPENDED'],
            'types' => ['Hospital', 'Clinic', 'Health Center', 'Dispensary'],
        ]);
    }

    public function adminFacilitiesStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:Online,Offline'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'create_account' => ['nullable', 'boolean'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'account_password' => ['nullable', 'string', 'min:8', 'max:255'],
        ]);

        Hospital::query()->create([
            'name' => $data['name'],
            'location' => $data['location'],
            'type' => $data['type'],
            'status' => $data['status'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        if ($request->boolean('create_account')) {
            $request->validate([
                'account_name' => ['required', 'string', 'max:255'],
                'account_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'account_password' => ['required', 'string', 'min:8', 'max:255'],
            ]);

            User::query()->create([
                'name' => (string) $request->string('account_name'),
                'full_name' => (string) $request->string('account_name'),
                'email' => (string) $request->string('account_email'),
                'role' => 'FACILITY',
                'status' => 'ACTIVE',
                'password' => Hash::make((string) $request->string('account_password')),
            ]);
        }

        return redirect()
            ->route('admin.facilities')
            ->with('status', __('roleui.facility_created_success'));
    }

    public function adminFacilitiesModerate(Request $request, Hospital $hospital): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'string', 'in:approve,reject,suspend,reactivate'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $action = (string) $data['action'];
        $note = trim((string) ($data['note'] ?? ''));
        $actor = $request->user();

        if ($action === 'approve') {
            $hospital->verification_status = 'APPROVED';
            $hospital->status = 'Online';
        } elseif ($action === 'reject') {
            $hospital->verification_status = 'REJECTED';
            $hospital->status = 'Offline';
        } elseif ($action === 'suspend') {
            $hospital->verification_status = 'SUSPENDED';
            $hospital->status = 'Offline';
        } elseif ($action === 'reactivate') {
            $hospital->verification_status = 'APPROVED';
            $hospital->status = 'Online';
        }

        $hospital->verified_at = now();
        $hospital->verified_by_user_id = $actor?->id;
        $hospital->verification_note = $note !== '' ? $note : null;
        $hospital->save();

        if ($hospital->owner_user_id) {
            $owner = User::query()->find($hospital->owner_user_id);
            if ($owner) {
                if (in_array($hospital->verification_status, ['APPROVED'], true)) {
                    $owner->status = 'ACTIVE';
                } elseif (in_array($hospital->verification_status, ['PENDING', 'REJECTED', 'SUSPENDED'], true)) {
                    $owner->status = 'PENDING';
                }
                $owner->save();
            }
        }

        return redirect()
            ->route('admin.facilities')
            ->with('status', __('roleui.facility_moderation_saved'));
    }

    public function adminAnalytics(): View
    {
        $totalUsers = User::query()->count();
        $totalFacilities = Hospital::query()->count();
        $totalSos = SosRequest::query()->count();
        $totalSubscribers = NewsletterSubscriber::query()->count();
        $totalContacts = ContactMessage::query()->count();
        $totalSymptoms = SafeGirlSymptom::query()->count();

        $latestUsers = User::query()
            ->select(['name', 'email', 'role', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $latestSos = SosRequest::query()
            ->select(['phone', 'address', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('role.admin.analytics', [
            'stats' => [
                'users' => $totalUsers,
                'facilities' => $totalFacilities,
                'sos' => $totalSos,
                'subscribers' => $totalSubscribers,
                'contacts' => $totalContacts,
                'symptoms' => $totalSymptoms,
            ],
            'latestUsers' => $latestUsers,
            'latestSos' => $latestSos,
        ]);
    }

    public function adminAuditLogs(): View
    {
        $events = collect()
            ->concat(
                User::query()
                    ->select(['id', 'name', 'email', 'role', 'created_at'])
                    ->latest()
                    ->limit(20)
                    ->get()
                    ->map(fn (User $user) => [
                        'type' => 'user_created',
                        'label' => __('roleui.audit_user_created'),
                        'summary' => trim($user->name.' ('.$user->email.')'),
                        'meta' => $user->role,
                        'created_at' => $user->created_at,
                    ])
            )
            ->concat(
                Hospital::query()
                    ->select(['id', 'name', 'location', 'status', 'created_at'])
                    ->latest()
                    ->limit(20)
                    ->get()
                    ->map(fn (Hospital $facility) => [
                        'type' => 'facility_created',
                        'label' => __('roleui.audit_facility_created'),
                        'summary' => trim($facility->name.' - '.$facility->location),
                        'meta' => $facility->status,
                        'created_at' => $facility->created_at,
                    ])
            )
            ->concat(
                SosRequest::query()
                    ->select(['id', 'phone', 'address', 'created_at'])
                    ->latest()
                    ->limit(20)
                    ->get()
                    ->map(fn (SosRequest $sos) => [
                        'type' => 'sos_created',
                        'label' => __('roleui.audit_sos_created'),
                        'summary' => trim(($sos->phone ?: 'N/A').' - '.($sos->address ?: 'N/A')),
                        'meta' => 'SOS',
                        'created_at' => $sos->created_at,
                    ])
            )
            ->concat(
                ContactMessage::query()
                    ->select(['id', 'name', 'email', 'subject', 'created_at'])
                    ->latest()
                    ->limit(20)
                    ->get()
                    ->map(fn (ContactMessage $contact) => [
                        'type' => 'contact_created',
                        'label' => __('roleui.audit_contact_created'),
                        'summary' => trim($contact->name.' ('.$contact->email.')'),
                        'meta' => $contact->subject ?: 'Contact',
                        'created_at' => $contact->created_at,
                    ])
            )
            ->concat(
                NewsletterSubscriber::query()
                    ->select(['id', 'email', 'created_at'])
                    ->latest()
                    ->limit(20)
                    ->get()
                    ->map(fn (NewsletterSubscriber $subscriber) => [
                        'type' => 'subscriber_created',
                        'label' => __('roleui.audit_subscriber_created'),
                        'summary' => $subscriber->email,
                        'meta' => 'Newsletter',
                        'created_at' => $subscriber->created_at,
                    ])
            )
            ->sortByDesc('created_at')
            ->take(60)
            ->values();

        return view('role.admin.audit-logs', [
            'events' => $events,
        ]);
    }

    public function adminBillingIntegrations(): View
    {
        $stats = [
            'users' => User::query()->count(),
            'facilities' => Hospital::query()->count(),
            'subscribers' => NewsletterSubscriber::query()->count(),
        ];

        $integrations = [
            [
                'name' => 'OpenAI',
                'key' => 'OPENAI_API_KEY',
                'configured' => (string) env('OPENAI_API_KEY', '') !== '',
            ],
            [
                'name' => 'Gemini',
                'key' => 'GEMINI_API_KEY',
                'configured' => (string) env('GEMINI_API_KEY', '') !== '',
            ],
            [
                'name' => 'Groq',
                'key' => 'GROQ_API_KEY',
                'configured' => (string) env('GROQ_API_KEY', '') !== '',
            ],
            [
                'name' => 'Mistral',
                'key' => 'MISTRAL_API_KEY',
                'configured' => (string) env('MISTRAL_API_KEY', '') !== '',
            ],
            [
                'name' => 'DeepSeek',
                'key' => 'DEEPSEEK_API_KEY',
                'configured' => (string) env('DEEPSEEK_API_KEY', '') !== '',
            ],
            [
                'name' => 'OpenRouter',
                'key' => 'OPENROUTER_API_KEY',
                'configured' => (string) env('OPENROUTER_API_KEY', '') !== '',
            ],
            [
                'name' => 'XAI',
                'key' => 'XAI_API_KEY',
                'configured' => (string) env('XAI_API_KEY', '') !== '',
            ],
        ];

        return view('role.admin.billing-integrations', [
            'stats' => $stats,
            'integrations' => $integrations,
        ]);
    }

    public function doctorDashboard(): View
    {
        /** @var User $doctor */
        $doctor = request()->user();
        $today = now()->toDateString();

        $profile = MedicalProfile::query()
            ->where('user_id', $doctor->id)
            ->first();

        $todayAppointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', $today)
            ->count();

        $upcomingAppointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', '>=', $today)
            ->count();

        $activeVideoSessions = VideoSession::query()
            ->where('doctor_id', $doctor->id)
            ->whereNull('end_time')
            ->count();

        $totalPatients = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->distinct('patient_id')
            ->count('patient_id');

        $nextAppointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', '>=', $today)
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(8)
            ->get();

        $patientNames = User::query()
            ->whereIn('id', $nextAppointments->pluck('patient_id')->filter()->values())
            ->pluck('name', 'id');

        $videoRequests = VideoSession::query()
            ->where('doctor_id', $doctor->id)
            ->whereNull('end_time')
            ->latest('id')
            ->limit(5)
            ->get(['id', 'patient_id', 'room_id', 'start_time']);

        $videoPatientNames = User::query()
            ->whereIn('id', $videoRequests->pluck('patient_id')->filter()->values())
            ->pluck('name', 'id');

        return view('role.doctor.dashboard', [
            'profile' => $profile,
            'stats' => [
                'today_appointments' => $todayAppointments,
                'upcoming_appointments' => $upcomingAppointments,
                'active_video_sessions' => $activeVideoSessions,
                'patients' => $totalPatients,
            ],
            'nextAppointments' => $nextAppointments,
            'patientNames' => $patientNames,
            'videoRequests' => $videoRequests,
            'videoPatientNames' => $videoPatientNames,
        ]);
    }

    public function doctorAppointments(): View
    {
        /** @var User $doctor */
        $doctor = request()->user();
        $today = now()->toDateString();

        $appointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->limit(200)
            ->get();

        $patients = User::query()
            ->where('role', 'PATIENT')
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->select(['id', 'name', 'email', 'phone'])
            ->limit(500)
            ->get();

        $patientNames = User::query()
            ->whereIn('id', $appointments->pluck('patient_id')->filter()->values())
            ->pluck('name', 'id');

        return view('role.doctor.appointments', [
            'appointments' => $appointments,
            'patients' => $patients,
            'patientNames' => $patientNames,
            'today' => $today,
        ]);
    }

    public function doctorAppointmentsStore(Request $request): RedirectResponse
    {
        /** @var User $doctor */
        $doctor = $request->user();

        $data = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:users,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $patient = User::query()->findOrFail((int) $data['patient_id']);
        abort_unless((string) $patient->role === 'PATIENT', 422, 'Selected user is not a patient.');

        Appointment::query()->create([
            'patient_id' => (int) $data['patient_id'],
            'doctor_id' => (int) $doctor->id,
            'appointment_date' => (string) $data['appointment_date'],
            'appointment_time' => (string) $data['appointment_time'],
            'reason' => ($data['reason'] ?? null) ?: null,
            'status' => 'PENDING',
            'created_at' => now(),
        ]);

        return redirect()
            ->route('doctor.appointments')
            ->with('status', 'Appointment created successfully.');
    }

    public function doctorPatients(): View
    {
        /** @var User $doctor */
        $doctor = request()->user();

        $patientIds = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->pluck('patient_id')
            ->filter()
            ->unique()
            ->values();

        $patients = User::query()
            ->whereIn('id', $patientIds)
            ->select(['id', 'name', 'email', 'phone', 'status', 'created_at'])
            ->orderBy('name')
            ->get();

        $appointmentCounts = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->selectRaw('patient_id, COUNT(*) as total')
            ->groupBy('patient_id')
            ->pluck('total', 'patient_id');

        $lastSeen = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->get(['patient_id', 'appointment_date', 'appointment_time'])
            ->groupBy('patient_id')
            ->map(function ($rows) {
                $row = $rows->first();
                if (! $row) {
                    return null;
                }

                return trim((string) $row->appointment_date.' '.(string) $row->appointment_time);
            });

        return view('role.doctor.patients', [
            'patients' => $patients,
            'appointmentCounts' => $appointmentCounts,
            'lastSeen' => $lastSeen,
        ]);
    }

    public function doctorCompleteProfile(): View
    {
        /** @var User $doctor */
        $doctor = request()->user();
        $profile = MedicalProfile::query()->where('user_id', $doctor->id)->first();

        return view('role.doctor.complete-profile', [
            'profile' => $profile,
        ]);
    }

    public function doctorCompleteProfileSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'staff_type' => ['required', 'string', 'max:50'],
            'specialization' => ['required', 'string', 'max:255'],
            'registration_no' => ['required', 'string', 'max:100'],
            'license_copy' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $existing = MedicalProfile::query()->where('user_id', $request->user()->id)->first();

        $path = $existing?->license_copy;
        if ($request->hasFile('license_copy')) {
            $path = $request->file('license_copy')->store('licenses', 'public');
        }

        MedicalProfile::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'staff_type' => $data['staff_type'],
                'specialization' => $data['specialization'],
                'registration_no' => $data['registration_no'],
                'license_copy' => (string) $path,
                'verification_status' => 'PENDING',
                'status' => 'PENDING',
                'created_at' => $existing?->created_at ?? now(),
                'updated_at' => now(),
            ],
        );

        return redirect()->route('doctor.dashboard')->with('status', __('roleui.profile_submitted'));
    }

    public function patientDashboard(): View
    {
        return view('role.patient.dashboard');
    }

    public function facilityDashboard(): View
    {
        return view('role.facility.dashboard');
    }
}