<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = [
        'context',
        'provider',
        'model',
        'api_key_encrypted',
        'is_enabled',
        'system_prompt',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];
}