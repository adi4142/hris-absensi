<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Departement;
use App\Division;
use App\Position;
use App\Attendance;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    protected $fillable = [
        'nip',
        'name',
        'user_id',
        'email',
        'phone',
        'departement_id',
        'position_id',
        'division_id',
        'address',
        'date_of_birth',
        'gender',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

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

    public function attendance(){
        return $this->hasMany(Attendance::class, 'employee_nip', 'nip');
    }

}
