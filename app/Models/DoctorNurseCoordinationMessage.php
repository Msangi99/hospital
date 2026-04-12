<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorNurseCoordinationMessage extends Model
{
    protected $fillable = [
        'coordination_chat_id',
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

    public function attachmentDownloadUrl(DoctorNurseCoordinationChat $chat): ?string
    {
        if (! $this->hasAttachment()) {
            return null;
        }

        return route('portal.coordination.messages.attachment', [
            'chat' => $chat->id,
            'message' => $this->id,
        ]);
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(DoctorNurseCoordinationChat::class, 'coordination_chat_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
