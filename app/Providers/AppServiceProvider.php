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

            $latest = VideoSession::query()
                ->where('doctor_id', $user->id)
                ->whereNull('end_time')
                ->latest('id')
                ->first(['patient_id', 'room_id']);

            if (! $latest) {
                $view->with('doctorInitialVideoToast', null);

                return;
            }

            $patientName = User::query()->whereKey($latest->patient_id)->value('name')
                ?? __('roleui.video_requests_unknown_patient');

            $view->with('doctorInitialVideoToast', [
                'patient_name' => (string) $patientName,
                'join_url' => route('doctor.video-consult', ['room' => $latest->room_id]),
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
