<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayrollComponent extends Model
{
    protected $table = 'payrolls_component';

    protected $primaryKey = 'payroll_component_id';

    protected $fillable = [
        'payroll_detail_id',
        'name',
        'type',
        'calculation_type',
        'calculation_value',
        'amount',
    ];

    public function payrollDetail()
    {
        return $this->belongsTo(PayrollDetail::class, 'payroll_detail_id', 'payroll_detail_id');
    }
}
