<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafeGirlSymptom extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'ip_address',
        'user_agent',
    ];
}

