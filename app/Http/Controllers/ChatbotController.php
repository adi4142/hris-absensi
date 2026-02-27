<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\LeaveRequest;
use App\PayrollDetail;
use App\PayrollComponent;
use App\Attendance;
use App\User;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $message = $request->message;

        $context = $this->gatherContext($user);

        $reply = $this->sendToGemini($message, $context, $user);

        return response()->json([
            'reply'  => $reply,
            'status' => 'success'
        ]);
    }

    /**
     * ===============================
     * KUMPULKAN DATA CONTEXT USER
     * ===============================
     */
    protected function gatherContext($user)
    {
        $context = [
            'profile' => [
                'name'          => $user->name,
                'nip'           => $user->nip,
                'department'    => optional($user->departement)->name,
                'position'      => optional($user->position)->name,
                'division'      => optional($user->division)->name,
                'phone'         => $user->phone,
                'address'       => $user->address,
                'gender'        => $user->gender,
                'basic_salary'  => $user->basic_salary,
                'jatah_cuti'    => $user->total_jatah_cuti,
                'cuti_terpakai' => $user->cuti_terpakai,
                'sisa_cuti'     => $user->sisa_cuti,
            ]
        ];

        // ── Payroll: 3 periode terakhir ──────────────────────────────
        try {
            $payrolls = PayrollDetail::with(['payroll', 'components'])
                ->where('nip', $user->nip)
                ->orderBy('payroll_detail_id', 'desc')
                ->take(3)
                ->get();

            if ($payrolls->isNotEmpty()) {
                $context['payroll_history'] = $payrolls->map(function ($p) {
                    $period = $p->payroll
                        ? ($p->payroll->period_month . ' ' . $p->payroll->period_year)
                        : 'N/A';

                    return [
                        'period'           => $period,
                        'basic_salary'     => (float) $p->basic_salary,
                        'allowance_total'  => (float) $p->total_allowance,
                        'deduction_total'  => (float) $p->total_deduction,
                        'net_salary'       => (float) $p->total_salary,
                        'components'       => $p->components->map(function ($c) {
                            return [
                                'name'   => $c->name,
                                'type'   => $c->type, // allowance | deduction
                                'amount' => (float) $c->amount,
                            ];
                        })->values(),
                    ];
                })->values();
            } else {
                $context['payroll_history'] = [];
            }
        } catch (\Exception $e) {
            Log::warning('ChatbotController: payroll query error – ' . $e->getMessage());
            $context['payroll_history'] = [];
        }

        // ── Cuti (LeaveRequest): 5 terakhir ─────────────────────────
        try {
            $leaves = LeaveRequest::where('nip', $user->nip)
                ->orderBy('start_date', 'desc')
                ->take(5)
                ->get();

            if ($leaves->isNotEmpty()) {
                $context['leave_history'] = $leaves->map(function ($l) {
                    return [
                        'start_date' => $l->start_date,
                        'end_date'   => $l->end_date,
                        'days'       => $l->days,
                        'reason'     => $l->reason,
                        'status'     => $l->status,    // PENDING | APPROVED | REJECTED
                        'hrd_note'   => $l->hrd_note,
                    ];
                })->values();
            } else {
                $context['leave_history'] = [];
            }
        } catch (\Exception $e) {
            Log::warning('ChatbotController: leave query error – ' . $e->getMessage());
            $context['leave_history'] = [];
        }

        // ── Absensi: 14 hari terakhir ────────────────────────────────
        try {
            $attendances = Attendance::where('employee_nip', $user->nip)
                ->orderBy('date', 'desc')
                ->take(14)
                ->get();

            if ($attendances->isNotEmpty()) {
                $context['attendance_history'] = $attendances->map(function ($a) {
                    return [
                        'date'         => $a->date,
                        'time_in'      => $a->time_in,
                        'time_out'     => $a->time_out,
                        'status'       => $a->status, // Present|Late|Excused|Sick|Permission|Alpha
                        'location_in'  => $a->location_in,
                        'location_out' => $a->location_out,
                        'description'  => $a->description ?? null,
                    ];
                })->values();

                // Ringkasan statistik absensi bulan ini
                $thisMonth = now()->format('Y-m');
                $monthlyAttendances = Attendance::where('employee_nip', $user->nip)
                    ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$thisMonth])
                    ->get();

                $context['attendance_summary_this_month'] = [
                    'period'      => now()->translatedFormat('F Y'),
                    'total_hadir' => $monthlyAttendances->whereIn('status', ['Present', 'Late'])->count(),
                    'hadir'       => $monthlyAttendances->where('status', 'Present')->count(),
                    'telat'       => $monthlyAttendances->where('status', 'Late')->count(),
                    'sakit'       => $monthlyAttendances->where('status', 'Sick')->count(),
                    'izin'        => $monthlyAttendances->where('status', 'Permission')->count(),
                    'excused'     => $monthlyAttendances->where('status', 'Excused')->count(),
                    'alpha'       => $monthlyAttendances->where('status', 'Alpha')->count(),
                ];
            } else {
                $context['attendance_history'] = [];
            }
        } catch (\Exception $e) {
            Log::warning('ChatbotController: attendance query error – ' . $e->getMessage());
            $context['attendance_history'] = [];
        }

        // ── Data lengkap semua karyawan untuk HRD / Admin / SuperAdmin ─
        $roleName = strtolower(optional($user->role)->name ?? '');
        if (in_array($roleName, ['hrd', 'superadmin', 'admin'])) {
            try {
                $today     = now()->toDateString();
                $thisMonth = now()->format('Y-m');

                // Statistik ringkasan
                $context['system_stats'] = [
                    'total_employees'          => User::count(),
                    'employees_on_leave_today' => LeaveRequest::where('status', 'APPROVED')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->count(),
                    'pending_leave_requests'   => LeaveRequest::where('status', 'PENDING')->count(),
                    'hadir_today'              => Attendance::whereDate('date', $today)->count(),
                    'late_today'               => Attendance::whereDate('date', $today)
                        ->where('status', 'Late')->count(),
                    'alpha_today'              => Attendance::whereDate('date', $today)
                        ->where('status', 'Alpha')->count(),
                ];

                // Data detail seluruh karyawan (agar bisa jawab pertanyaan spesifik per karyawan)
                $allEmployees = User::with(['role', 'departement', 'position', 'division'])
                    ->whereNotNull('nip')
                    ->get();

                $employeesData = [];
                foreach ($allEmployees as $emp) {
                    // Payroll terakhir
                    $lastPayroll = PayrollDetail::with(['payroll', 'components'])
                        ->where('nip', $emp->nip)
                        ->orderBy('payroll_detail_id', 'desc')
                        ->first();

                    $payrollData = null;
                    if ($lastPayroll) {
                        $period = $lastPayroll->payroll
                            ? ($lastPayroll->payroll->period_month . ' ' . $lastPayroll->payroll->period_year)
                            : 'N/A';
                        $payrollData = [
                            'period'          => $period,
                            'basic_salary'    => (float) $lastPayroll->basic_salary,
                            'allowance_total' => (float) $lastPayroll->total_allowance,
                            'deduction_total' => (float) $lastPayroll->total_deduction,
                            'net_salary'      => (float) $lastPayroll->total_salary,
                            'components'      => $lastPayroll->components->map(function ($c) {
                                return [
                                    'name'   => $c->name,
                                    'type'   => $c->type,
                                    'amount' => (float) $c->amount,
                                ];
                            })->values()->toArray(),
                        ];
                    }

                    // Absensi bulan ini
                    $monthlyAtt = Attendance::where('employee_nip', $emp->nip)
                        ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$thisMonth])
                        ->get();

                    // Cuti aktif / terbaru
                    $latestLeave = LeaveRequest::where('nip', $emp->nip)
                        ->orderBy('start_date', 'desc')
                        ->first();

                    $employeesData[] = [
                        'name'          => $emp->name,
                        'nip'           => $emp->nip,
                        'department'    => optional($emp->departement)->name,
                        'position'      => optional($emp->position)->name,
                        'division'      => optional($emp->division)->name,
                        'gender'        => $emp->gender,
                        'basic_salary'  => (float) $emp->basic_salary,
                        'jatah_cuti'    => $emp->total_jatah_cuti,
                        'cuti_terpakai' => $emp->cuti_terpakai,
                        'sisa_cuti'     => $emp->sisa_cuti,
                        'last_payroll'  => $payrollData,
                        'attendance_this_month' => [
                            'hadir'   => $monthlyAtt->where('status', 'Present')->count(),
                            'telat'   => $monthlyAtt->where('status', 'Late')->count(),
                            'sakit'   => $monthlyAtt->where('status', 'Sick')->count(),
                            'izin'    => $monthlyAtt->where('status', 'Permission')->count(),
                            'excused' => $monthlyAtt->where('status', 'Excused')->count(),
                            'alpha'   => $monthlyAtt->where('status', 'Alpha')->count(),
                        ],
                        'latest_leave'  => $latestLeave ? [
                            'start_date' => $latestLeave->start_date,
                            'end_date'   => $latestLeave->end_date,
                            'days'       => $latestLeave->days,
                            'reason'     => $latestLeave->reason,
                            'status'     => $latestLeave->status,
                        ] : null,
                    ];
                }

                $context['all_employees'] = $employeesData;

            } catch (\Exception $e) {
                Log::warning('ChatbotController: admin context error – ' . $e->getMessage());
            }
        }

        return $context;
    }

    /**
     * ===============================
     * KIRIM KE GEMINI
     * ===============================
     */
    protected function sendToGemini($message, $context, $user)
    {
        $apiKey = config('services.gemini.key');
        $apiUrl = config('services.gemini.url');

        $role = strtolower(optional($user->role)->name ?? '');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini config missing: key or url not set in config/services.php');
            return $this->fallback($message, $context, $user);
        }

        $systemPrompt = $this->buildPrompt($context, $user, $role);

        try {
            $response = Http::timeout(30)
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [
                        [
                            'role'  => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nPertanyaan user: " . $message]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.5,
                        'maxOutputTokens' => 1024,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $this->formatReply(
                        $data['candidates'][0]['content']['parts'][0]['text']
                    );
                }

                Log::warning('Gemini: unexpected response structure', ['body' => $data]);
            }

            Log::error('Gemini HTTP error', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);

            return $this->fallback($message, $context, $user);

        } catch (\Exception $e) {
            Log::error('Gemini exception: ' . $e->getMessage());
            return $this->fallback($message, $context, $user);
        }
    }

    /**
     * ===============================
     * BUILD PROMPT
     * ===============================
     */
    protected function buildPrompt($context, $user, $role)
    {
        $contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $roleName    = strtolower($role);
        $today       = now()->translatedFormat('d F Y');

        if (in_array($roleName, ['hrd', 'superadmin', 'admin'])) {
            return <<<PROMPT
Kamu adalah HR Expert Assistant untuk sistem HRIS perusahaan.
Tugas kamu membantu manajemen menganalisis data karyawan, penggajian, dan absensi.
Tanggal hari ini: {$today}

DATA SISTEM LENGKAP (JSON):
{$contextJson}

STRUKTUR DATA:
- system_stats: statistik ringkasan seluruh karyawan hari ini
- all_employees: array berisi data detail SETIAP karyawan, termasuk:
  * name, nip, department, position, division, gender
  * basic_salary: gaji pokok dari profil user
  * jatah_cuti, cuti_terpakai, sisa_cuti: informasi cuti
  * last_payroll: data gaji bulan terakhir (basic_salary, allowance_total, deduction_total, net_salary, components)
  * attendance_this_month: rekap absensi bulan ini (hadir, telat, sakit, izin, alpha)
  * latest_leave: cuti/izin terakhir beserta statusnya

INSTRUKSI:
1. Jawab dalam Bahasa Indonesia yang profesional dan ringkas.
2. Saat ditanya tentang karyawan tertentu, cari namanya di dalam array all_employees (bandingkan nama secara case-insensitive, bisa sebagian nama).
3. Gunakan DATA SISTEM di atas sebagai satu-satunya sumber kebenaran. Jangan mengarang angka.
4. Jika data karyawan tidak ditemukan di all_employees, katakan dengan jujur.
5. Format jawaban dengan Markdown: gunakan **bold** untuk angka/nama penting, bullet untuk list.
6. Untuk angka rupiah, format dalam ribuan (contoh: Rp 5.000.000).
7. Untuk pertanyaan tentang gaji: gunakan data last_payroll jika tersedia, jika tidak gunakan basic_salary dari profil.

Nama Admin/HRD: {$user->name}
PROMPT;
        }

        return <<<PROMPT
Kamu adalah HR Assistant yang ramah untuk karyawan sistem HRIS.
Tugas kamu membantu karyawan mendapatkan informasi pribadi mereka (gaji, cuti, absensi).
Tanggal hari ini: {$today}

DATA PRIBADI KARYAWAN (JSON):
{$contextJson}

INSTRUKSI:
1. Jawab dalam Bahasa Indonesia yang ramah, hangat, dan terstruktur.
2. Gunakan DATA PRIBADI di atas sebagai satu-satunya sumber kebenaran. Jangan mengarang angka.
3. Jika ditanya tentang gaji: rujuk ke payroll_history (periode, gaji pokok, tunjangan, potongan, gaji bersih, komponen).
4. Jika ditanya tentang cuti: rujuk ke leave_history (tanggal, durasi, alasan, status persetujuan) dan profile (jatah_cuti, sisa_cuti).
5. Jika ditanya tentang absensi: rujuk ke attendance_history dan attendance_summary_this_month.
6. Jika data tidak ada, sarankan menghubungi HRD.
7. Format jawaban dengan Markdown: **bold** untuk angka/status penting, bullet untuk list.
8. Untuk angka rupiah, format dalam ribuan (contoh: Rp 5.000.000).

Nama Karyawan: {$user->name}
PROMPT;
    }

    /**
     * ===============================
     * FORMAT REPLY (Markdown → HTML)
     * ===============================
     */
    protected function formatReply($reply)
    {
        // Bold: **text** → <b>text</b>
        $reply = preg_replace('/\*\*(.*?)\*\*/s', '<b>$1</b>', $reply);
        // Italic: *text* → <i>text</i>
        $reply = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '<i>$1</i>', $reply);
        // List items: "- item" or "* item" → bullet point
        $reply = preg_replace('/^[\-\*]\s+/m', '• ', $reply);
        // Strip any dangerous HTML, allow basic formatting
        $reply = strip_tags($reply, '<b><i><br><ul><li><p>');
        // Convert newlines to <br>
        $reply = nl2br($reply);

        return $reply;
    }

    /**
     * ===============================
     * FALLBACK (jika Gemini gagal)
     * ===============================
     */
    protected function fallback($message, $context, $user)
    {
        $msg = strtolower($message);

        // Helper: PHP 7 compatible strpos-based contains check
        $contains = function ($haystack, $needle) {
            return strpos($haystack, $needle) !== false;
        };

        // ── Pertanyaan tentang Gaji ───────────────────────────────────
        if (($contains($msg, 'gaji') || $contains($msg, 'payroll') || $contains($msg, 'slip'))
            && !empty($context['payroll_history'])) {

            $p = $context['payroll_history'][0];
            $components = '';
            if (!empty($p['components'])) {
                foreach ($p['components'] as $c) {
                    $label = $c['type'] === 'allowance' ? '🟢 Tunjangan' : '🔴 Potongan';
                    $components .= "• {$label} {$c['name']}: <b>Rp " . number_format($c['amount'], 0, ',', '.') . "</b><br>";
                }
            }

            return "
💰 <b>Informasi Gaji Terakhir</b> ({$p['period']}):<br><br>
• Gaji Pokok: <b>Rp " . number_format($p['basic_salary'], 0, ',', '.') . "</b><br>
• Total Tunjangan: <b>Rp " . number_format($p['allowance_total'], 0, ',', '.') . "</b><br>
• Total Potongan: <b>Rp " . number_format($p['deduction_total'], 0, ',', '.') . "</b><br>
• <b>Gaji Bersih: Rp " . number_format($p['net_salary'], 0, ',', '.') . "</b><br>
" . ($components ? "<br><b>Rincian Komponen:</b><br>{$components}" : '');
        }

        // ── Pertanyaan tentang Cuti ───────────────────────────────────
        if (($contains($msg, 'cuti') || $contains($msg, 'izin') || $contains($msg, 'leave'))
            && !empty($context['leave_history'])) {

            $l     = $context['leave_history'][0];
            $saldo = isset($context['profile']['sisa_cuti']) ? $context['profile']['sisa_cuti'] : '-';
            $status = strtoupper(isset($l['status']) ? $l['status'] : '');
            if ($status === 'APPROVED') {
                $statusIcon = '✅';
            } elseif ($status === 'REJECTED') {
                $statusIcon = '❌';
            } else {
                $statusIcon = '⏳';
            }

            return "
📅 <b>Informasi Cuti Terakhir:</b><br><br>
• Mulai: <b>{$l['start_date']}</b><br>
• Selesai: <b>{$l['end_date']}</b><br>
• Durasi: <b>{$l['days']} hari</b><br>
• Alasan: {$l['reason']}<br>
• Status: {$statusIcon} <b>{$l['status']}</b><br>
<br>
📊 <b>Saldo Cuti Anda:</b> {$saldo} hari tersisa
";
        }

        // ── Pertanyaan tentang Absensi ────────────────────────────────
        if (($contains($msg, 'absen') || $contains($msg, 'hadir') || $contains($msg, 'masuk') || $contains($msg, 'pulang'))
            && !empty($context['attendance_history'])) {

            $a       = $context['attendance_history'][0];
            $summary = isset($context['attendance_summary_this_month']) ? $context['attendance_summary_this_month'] : null;

            $summaryText = '';
            if ($summary) {
                $summaryText = "
<br>📊 <b>Ringkasan Bulan {$summary['period']}:</b><br>
• Hadir: <b>{$summary['hadir']}</b> hari<br>
• Telat: <b>{$summary['telat']}</b> hari<br>
• Sakit: <b>{$summary['sakit']}</b> hari<br>
• Izin: <b>{$summary['izin']}</b> hari<br>
• Alpha: <b>{$summary['alpha']}</b> hari<br>
";
            }

            $timeOut     = $a['time_out'] ? $a['time_out'] : 'Belum absen pulang';
            $description = $a['description'] ? "• Keterangan: {$a['description']}<br>" : '';

            return "
📋 <b>Absensi Terakhir ({$a['date']}):</b><br><br>
• Jam Masuk: <b>{$a['time_in']}</b><br>
• Jam Keluar: <b>{$timeOut}</b><br>
• Status: <b>{$a['status']}</b><br>
{$description}{$summaryText}";
        }

        // ── Default ───────────────────────────────────────────────────
        return "Halo, <b>{$user->name}</b>! 👋<br>
Saya adalah HR Assistant AI. Saya bisa membantu Anda informasi seputar:<br>
• 💰 <b>Gaji &amp; Payroll</b> — tanya \"berapa gaji saya?\"<br>
• 📅 <b>Cuti</b> — tanya \"status cuti saya?\" atau \"sisa cuti saya?\"<br>
• 📋 <b>Absensi</b> — tanya \"rekap absensi saya?\"<br>
<br>
Saat ini layanan AI sedang dalam kendala teknis. Silakan coba beberapa saat lagi atau hubungi HRD.";
    }
}