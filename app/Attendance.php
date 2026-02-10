<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';
    protected $fillable = [
        'employee_nip',
        'date',
        'time_in',
        'time_out',
        'photo_in',
        'photo_out',
        'location_in',
        'location_out',
        'status',
        'description',
        'proof_file'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_nip', 'nip');
    }
}
