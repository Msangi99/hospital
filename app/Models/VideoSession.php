<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoSession extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'room_id',
        'start_time',
        'end_time',
    ];
}

