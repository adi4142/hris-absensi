<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BasicSalary extends Model
{
    protected $table = 'basic_salaries';

    protected $fillable = [
        'user_nip',
        'basic_salary',
        'effective_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_nip', 'nip');
    }
}
