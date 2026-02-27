<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSuperadminFeaturesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tambahkan kolom is_locked ke tabel payrolls
        if (Schema::hasTable('payrolls')) {
            Schema::table('payrolls', function (Blueprint $table) {
                if (!Schema::hasColumn('payrolls', 'is_locked')) {
                    $table->boolean('is_locked')->default(false)->after('status');
                }
            });
        }

        // Tambahkan pengaturan sistem default ke tabel system_settings
        if (Schema::hasTable('system_settings')) {
            $settings = [
                [
                    'key' => 'work_start_time',
                    'value' => '08:00',
                    'group' => 'attendance',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'work_end_time',
                    'value' => '17:00',
                    'group' => 'attendance',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'attendance_radius',
                    'value' => '100', // meter
                    'group' => 'attendance',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'office_latitude',
                    'value' => '-6.200000',
                    'group' => 'attendance',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'office_longitude',
                    'value' => '106.816666',
                    'group' => 'attendance',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'late_deduction_amount',
                    'value' => '5000',
                    'group' => 'payroll',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($settings as $setting) {
                // Gunakan updateOrInsert agar tidak duplikat jika dijalankan ulang
                DB::table('system_settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    $setting
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('payrolls')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropColumn('is_locked');
            });
        }
        
        // Catatan: Down untuk system_settings biasanya tidak menghapus baris data 
        // agar tidak menghilangkan konfigurasi yang sudah diset user.
    }
}
