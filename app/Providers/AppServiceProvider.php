<?php

namespace App\Providers;

use App\Models\User;
use App\Models\VideoSession;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthHooks();
        $this->configureDoctorVideoToastComposer();
    }

    protected function configureDoctorVideoToastComposer(): void
    {
        View::composer('layouts.role-dashboard', function (\Illuminate\View\View $view): void {
            $user = auth()->user();
            if (! $user || (string) ($user->role ?? '') !== 'MEDICAL_TEAM') {
                $view->with('doctorInitialVideoToast', null);

                return;
            }

            $latestRinging = VideoSession::query()
                ->where('doctor_id', $user->id)
                ->whereNull('end_time')
                ->whereNull('doctor_joined_at')
                ->where('start_time', '>', now()->subSeconds(VideoSession::DOCTOR_RING_GRACE_SECONDS))
                ->latest('id')
                ->first(['id', 'patient_id', 'room_id', 'start_time', 'doctor_joined_at', 'end_time']);

            if (! $latestRinging) {
                $view->with('doctorInitialVideoToast', null);

                return;
            }

            $patientName = User::query()->whereKey($latestRinging->patient_id)->value('name')
                ?? __('roleui.video_requests_unknown_patient');

            $view->with('doctorInitialVideoToast', [
                'video_session_id' => (int) $latestRinging->id,
                'patient_name' => (string) $patientName,
                'join_url' => route('doctor.video-consult', ['room' => $latestRinging->room_id]),
            ]);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureAuthHooks(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            $user = $event->user;

            /** @var Model $user */
            $user->forceFill(['last_login_at' => now()])->save();
        });
    }
}
