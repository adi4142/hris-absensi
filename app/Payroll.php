<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payrolls';

    protected $primaryKey = 'payroll_id';

    protected $fillable = [
        'period_month',
        'period_year',
        'status',
        'is_locked',
    ];

    public function details()
    {
        return $this->hasMany(PayrollDetail::class, 'payroll_id', 'payroll_id');
    }

    /**
     * Menghitung Gaji Bersih
     * Rumus: (Gaji Pokok + Tunjangan) - Potongan
     */
    public function hitungGajiBersih($gajiPokok, $potongan, $tunjangan = 0)
    {
        return ($gajiPokok + $tunjangan) - $potongan;
    }
}
