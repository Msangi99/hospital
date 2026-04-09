<?php

namespace App\Livewire\Settings;

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    use PasswordValidationRules;

    public string $password = '';

    public bool $confirmingDeletion = false;

    public function startConfirmingDeletion(): void
    {
        $this->confirmingDeletion = true;
        $this->resetErrorBag();
        $this->password = '';
    }

    public function cancelDeletion(): void
    {
        $this->confirmingDeletion = false;
        $this->password = '';
        $this->resetErrorBag();
    }

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}
