<?php

use App\Http\Controllers\AdminConsoleController;
use App\Http\Controllers\PublicPagesController;
use App\Http\Controllers\RolePagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPagesController::class, 'home'])->name('home');

Route::get('/about', [PublicPagesController::class, 'about'])->name('about');
Route::get('/services', [PublicPagesController::class, 'services'])->name('services');
Route::get('/hospitals', [PublicPagesController::class, 'hospitals'])->name('hospitals');
Route::get('/ambulance', [PublicPagesController::class, 'ambulance'])->name('ambulance');
Route::post('/ambulance/sos', [PublicPagesController::class, 'ambulanceSos'])->name('ambulance.sos');
Route::get('/contact', [PublicPagesController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicPagesController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/docs', [PublicPagesController::class, 'docs'])->name('docs');
Route::get('/privacy', [PublicPagesController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicPagesController::class, 'terms'])->name('terms');
Route::get('/ussd', [PublicPagesController::class, 'ussd'])->name('ussd');
Route::get('/ussd-info', [PublicPagesController::class, 'ussdInfo'])->name('ussd.info');
Route::get('/safe-girl', [PublicPagesController::class, 'safeGirl'])->name('safe-girl');
Route::get('/video-consult', [PublicPagesController::class, 'videoConsult'])
    ->middleware(['auth'])
    ->name('video-consult');
Route::post('/safe-girl/symptoms', [PublicPagesController::class, 'safeGirlSymptomSubmit'])
    ->middleware(['auth'])
    ->name('safe-girl.symptoms');
Route::post('/safe-girl/ai-chat', [PublicPagesController::class, 'safeGirlAiChat'])
    ->middleware(['auth'])
    ->name('safe-girl.ai-chat');
Route::post('/subscribe', [PublicPagesController::class, 'subscribe'])->name('subscribe');

Route::middleware(['guest'])->group(function () {
    Route::get('/register/hospital-owner', function () {
        return view('livewire.auth.register', ['registerType' => 'owner']);
    })->name('register.owner');
});

Route::post('/locale', function () {
    $locale = (string) request('locale', '');
    $allowed = ['en', 'fr', 'ar', 'sw'];

    if (! in_array($locale, $allowed, true)) {
        $locale = config('app.fallback_locale', 'en');
    }

    session(['locale' => $locale]);

    return back();
})->name('locale.set');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = request()->user();
        $role = (string) ($user->role ?? '');

        if ($role === 'SUPERADMIN') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'HOSPITAL_OWNER') {
            return redirect()->route('owner.dashboard');
        }

        if ($role === 'MEDICAL_TEAM') {
            return redirect()->route('doctor.dashboard');
        }

        if ($role === 'FACILITY') {
            return redirect()->route('facility.dashboard');
        }

        return redirect()->route('patient.dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [RolePagesController::class, 'adminDashboard'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.dashboard');
    Route::get('/admin/emergencies', [RolePagesController::class, 'adminEmergencies'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.emergencies');
    Route::get('/admin/newsletter', [RolePagesController::class, 'adminNewsletter'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.newsletter');
    Route::get('/admin/alerts', [RolePagesController::class, 'adminAlerts'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.alerts');
    Route::get('/admin/ai', [RolePagesController::class, 'adminAiSettings'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.ai-settings');
    Route::post('/admin/ai', [RolePagesController::class, 'adminAiSettingsUpdate'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.ai-settings.update');
    Route::post('/admin/ai/models', [RolePagesController::class, 'adminAiModelsFetch'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.ai-settings.models');
    Route::get('/admin/users', [RolePagesController::class, 'adminUsers'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.users');
    Route::post('/admin/users', [RolePagesController::class, 'adminUsersStore'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.users.store');
    Route::get('/admin/facilities', [RolePagesController::class, 'adminFacilities'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.facilities');
    Route::post('/admin/facilities', [RolePagesController::class, 'adminFacilitiesStore'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.facilities.store');
    Route::post('/admin/facilities/{hospital}/moderate', [RolePagesController::class, 'adminFacilitiesModerate'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.facilities.moderate');
    Route::get('/admin/analytics', [RolePagesController::class, 'adminAnalytics'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.analytics');
    Route::get('/admin/audit-logs', [RolePagesController::class, 'adminAuditLogs'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.audit-logs');
    Route::get('/admin/billing-integrations', [RolePagesController::class, 'adminBillingIntegrations'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.billing-integrations');

    Route::get('/admin/console', [AdminConsoleController::class, 'index'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.console');
    Route::post('/admin/console/migrate', [AdminConsoleController::class, 'migrate'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.console.migrate');
    Route::post('/admin/console/migrate-path', [AdminConsoleController::class, 'migratePath'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.console.migrate-path');
    Route::post('/admin/console/seed', [AdminConsoleController::class, 'seedAll'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.console.seed');
    Route::post('/admin/console/seed-class', [AdminConsoleController::class, 'seedClass'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.console.seed-class');
    Route::post('/admin/console/tool', [AdminConsoleController::class, 'tool'])
        ->middleware(['role:SUPERADMIN'])
        ->name('admin.console.tool');

    Route::get('/owner', [RolePagesController::class, 'hospitalOwnerDashboard'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->name('owner.dashboard');
    Route::post('/owner/profile', [RolePagesController::class, 'hospitalOwnerProfileUpdate'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->name('owner.profile.update');
    Route::get('/owner/workers', [RolePagesController::class, 'hospitalOwnerWorkers'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->name('owner.workers');
    Route::post('/owner/workers', [RolePagesController::class, 'hospitalOwnerWorkersStore'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->name('owner.workers.store');
    Route::get('/owner/departments', [RolePagesController::class, 'hospitalOwnerSection'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->defaults('section', 'departments')
        ->name('owner.departments');
    Route::get('/owner/services', [RolePagesController::class, 'hospitalOwnerSection'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->defaults('section', 'services')
        ->name('owner.services');
    Route::get('/owner/schedules', [RolePagesController::class, 'hospitalOwnerSection'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->defaults('section', 'schedules')
        ->name('owner.schedules');
    Route::get('/owner/reports', [RolePagesController::class, 'hospitalOwnerSection'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->defaults('section', 'reports')
        ->name('owner.reports');
    Route::get('/owner/billing', [RolePagesController::class, 'hospitalOwnerSection'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->defaults('section', 'billing')
        ->name('owner.billing');
    Route::get('/owner/settings', [RolePagesController::class, 'hospitalOwnerSection'])
        ->middleware(['role:HOSPITAL_OWNER'])
        ->defaults('section', 'settings')
        ->name('owner.settings');

    Route::get('/doctor', [RolePagesController::class, 'doctorDashboard'])
        ->middleware(['role:MEDICAL_TEAM'])
        ->name('doctor.dashboard');

    Route::get('/doctor/appointments', [RolePagesController::class, 'doctorAppointments'])
        ->middleware(['role:MEDICAL_TEAM'])
        ->name('doctor.appointments');

    Route::get('/doctor/patients', [RolePagesController::class, 'doctorPatients'])
        ->middleware(['role:MEDICAL_TEAM'])
        ->name('doctor.patients');

    Route::get('/doctor/complete-profile', [RolePagesController::class, 'doctorCompleteProfile'])
        ->middleware(['role:MEDICAL_TEAM'])
        ->name('doctor.complete-profile');
    Route::post('/doctor/complete-profile', [RolePagesController::class, 'doctorCompleteProfileSubmit'])
        ->middleware(['role:MEDICAL_TEAM'])
        ->name('doctor.complete-profile.submit');

    Route::get('/patient', [RolePagesController::class, 'patientDashboard'])
        ->middleware(['role:PATIENT'])
        ->name('patient.dashboard');

    Route::get('/facility', [RolePagesController::class, 'facilityDashboard'])
        ->middleware(['role:FACILITY'])
        ->name('facility.dashboard');
});

require __DIR__.'/settings.php';