<?php

use App\Http\Controllers\PublicPagesController;
use App\Http\Controllers\RolePagesController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

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
Route::post('/subscribe', [PublicPagesController::class, 'subscribe'])->name('subscribe');

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
