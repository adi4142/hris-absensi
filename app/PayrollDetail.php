<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $table = 'payrolls_detail';

    protected $primaryKey = 'payroll_detail_id';

    protected $fillable = [
        'nip',
        'payroll_id',
        'basic_salary',
        'total_allowance',
        'total_deduction',
        'total_salary',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id', 'payroll_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'nip', 'nip');
    }

    public function components()
    {
        return $this->hasMany(PayrollComponent::class, 'payroll_detail_id', 'payroll_detail_id');
    }
}
