<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientDoctorConversation extends Model
{
    protected $table = 'patient_doctor_conversations';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'hospital_id',
        'title',
    ];

    public function displayTitle(): string
    {
        $t = trim((string) ($this->title ?? ''));
        if ($t !== '') {
            return $t;
        }

        $name = trim((string) ($this->patient?->name ?? ''));

        return $name !== '' ? $name : (string) __('roleui.conversations_untitled_chat');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PatientDoctorConversationMessage::class, 'conversation_id')->orderBy('id');
    }
}
