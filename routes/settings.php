<?php

use App\Livewire\Settings\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('/settings/prepare-admin-access', function (Request $request) {
        abort_unless((string) ($request->user()?->role ?? '') === 'SUPERADMIN', 403);

        $request->session()->put(
            'url.intended',
            route('profile.edit', ['admin_mfa' => 'setup'], absolute: false)
        );

        return redirect()->route('password.confirm');
    })->name('settings.prepare-admin-access');

    Route::livewire('settings/profile', Profile::class)->name('profile.edit');
});
