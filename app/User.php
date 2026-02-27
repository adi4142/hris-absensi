<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Role;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'users';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'name', 
        'email', 
        'password',
        'roles_id',
        'photo',
        'verification_code',
        'email_verified_at',
        'is_role_verified',
        'nip',
        'phone',
        'departement_id',
        'position_id',
        'division_id',
        'address',
        'date_of_birth',
        'gender',
        'attendance_code',
        'basic_salary',
        'total_jatah_cuti',
        'cuti_terpakai',
        'sisa_cuti',
    ];
    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'roles_id', 'roles_id');
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_role_verified' => 'boolean',
    ];

    public function departement(){
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function division(){
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function position(){
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function attendances(){
        return $this->hasMany(Attendance::class, 'employee_nip', 'nip');
    }

}
