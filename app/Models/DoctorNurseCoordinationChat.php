<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorNurseCoordinationChat extends Model
{
    protected $fillable = [
        'doctor_id',
        'nurse_id',
        'patient_id',
        'patient_context',
        'hospital_id',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DoctorNurseCoordinationMessage::class, 'coordination_chat_id')->orderBy('id');
    }

    public function threadTitle(): string
    {
        return (string) $this->patient_context;
    }
}
