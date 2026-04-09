<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalProfile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'staff_type',
        'specialization',
        'registration_no',
        'license_copy',
        'verification_status',
        'bio',
        'status',
        'created_at',
        'updated_at',
    ];
}

