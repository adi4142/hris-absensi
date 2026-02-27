<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';

    protected $fillable = [
        'nip',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'hrd_note',
    ];

    /**
     * Relasi ke User (Karyawan)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nip', 'nip');
    }

    // Konstanta untuk status cuti
    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
}
