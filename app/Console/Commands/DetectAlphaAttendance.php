<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;
use App\Attendance;
use App\EmployeeLeaves;
use Carbon\Carbon;

class DetectAlphaAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:detect-alpha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mendeteksi status absensi ALPHA karyawan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        $dateString = $today->toDateString();

        $this->info("Menjalankan deteksi ALPHA untuk tanggal: " . $dateString);

        // 1. Cek apakah akhir pekan (Sabtu atau Minggu)
        // Jika sistem Anda memiliki tabel 'holidays', Anda bisa menambahkan pengecekan di sini
        if ($today->isWeekend()) {
            $this->info("Hari ini adalah hari libur (weekend). Proses dihentikan.");
            return 0;
        }

        // 2. Ambil daftar NIP yang sudah memiliki catatan di tabel attendances (Hadir, Terlambat, atau Izin)
        $existingAttendanceNips = Attendance::whereDate('date', $dateString)
            ->pluck('employee_nip')
            ->toArray();

        // 3. Ambil daftar NIP yang sedang mengambil cuti/izin di tabel employees_leaves
        $onLeaveNips = EmployeeLeaves::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->pluck('nip')
            ->toArray();

        // Gabungkan NIP yang tidak perlu diproses ALPHA
        $excludedNips = array_unique(array_merge($existingAttendanceNips, $onLeaveNips));

        // 4. Ambil semua karyawan yang NIP-nya TIDAK ada di daftar pengecualian
        // Menggunakan chunking jika jumlah karyawan sangat banyak (opsional, di sini gunakan get() untuk kemudahan)
        $alphaEmployees = Employee::whereNotIn('nip', $excludedNips)->get();

        $totalAlpha = $alphaEmployees->count();
        $this->info("Ditemukan " . $totalAlpha . " karyawan tanpa status absensi hari ini.");

        if ($totalAlpha > 0) {
            $dataToInsert = [];
            foreach ($alphaEmployees as $employee) {
                $dataToInsert[] = [
                    'employee_nip' => $employee->nip,
                    'date'         => $dateString,
                    'time_in'      => null,
                    'time_out'     => null,
                    'status'       => 'Alpha',
                    'description'  => 'Tidak masuk tanpa keterangan (Otomatis Sistem)',
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ];
                
                $this->line("Menandai ALPHA: " . $employee->name . " (" . $employee->nip . ")");
            }

            // Bulk insert untuk optimalisasi performa (menghindari query insert berkali-kali dalam loop)
            Attendance::insert($dataToInsert);
            $this->info("Berhasil menyimpan " . $totalAlpha . " data ALPHA.");
        } else {
            $this->info("Tidak ada karyawan yang perlu ditandai ALPHA hari ini.");
        }

        return 0;
    }
}
