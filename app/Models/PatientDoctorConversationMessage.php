<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDoctorConversationMessage extends Model
{
    protected $table = 'patient_doctor_conversation_messages';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime',
        'attachment_kind',
        'attachment_size',
    ];

    protected function casts(): array
    {
        return [
            'attachment_size' => 'integer',
        ];
    }

    public function hasAttachment(): bool
    {
        return $this->attachment_path !== null && $this->attachment_path !== '';
    }

    public function attachmentDownloadUrl(PatientDoctorConversation $conversation): ?string
    {
        if (! $this->hasAttachment()) {
            return null;
        }

        return route('portal.conversations.messages.attachment', [
            'conversation' => $conversation->id,
            'message' => $this->id,
        ]);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(PatientDoctorConversation::class, 'conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
