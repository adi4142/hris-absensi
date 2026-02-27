<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveSetting extends Model
{
    protected $table = 'leave_settings';

    protected $fillable = [
        'annual_allowance',
        'can_carry_over',
        'max_days_per_request',
    ];

    protected $casts = [
        'can_carry_over' => 'boolean',
    ];
}
