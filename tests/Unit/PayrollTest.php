<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Payroll;

class PayrollTest extends TestCase
{
    public function test_hitung_gaji_bersih_dasar()
    {
        $payroll = new Payroll();

        $hasil = $payroll->hitungGajiBersih(5000000, 500000);

        $this->assertEquals(4500000, $hasil);
    }

    public function test_hitung_gaji_bersih_dengan_tunjangan()
    {
        $payroll = new Payroll();

        $hasil = $payroll->hitungGajiBersih(5000000, 250000, 1000000);

        $this->assertEquals(5750000, $hasil);
    }

    public function test_hitung_gaji_minus()
    {
        $payroll = new Payroll();

        $hasil = $payroll->hitungGajiBersih(1000000, 2000000);

        $this->assertEquals(-1000000, $hasil);
    }
}
