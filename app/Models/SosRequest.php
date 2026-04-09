<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosRequest extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'latitude',
        'longitude',
        'address',
        'ip_address',
        'user_agent',
    ];
}

