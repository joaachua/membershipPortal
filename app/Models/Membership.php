<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'membership_picture',
        'description',
        'duration_months',
        'monthly_fee',
        'personal_training',
        'personal_training_sessions_per_week',
        'secure_locker',
        'guest_access',
        'pay_as_you_go',
        'cancellation_policy',
    ];
}
