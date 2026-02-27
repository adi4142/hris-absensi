<?php

namespace App\Http\Middleware;

use Closure;
use App\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (in_array(strtolower($request->method()), ['post', 'put', 'delete', 'patch'])) {
            
            $action = strtoupper($request->method());
            $routeName = $request->route() ? $request->route()->getName() : null;
            $description = 'Mengakses endpoint: /' . ltrim($request->path(), '/');

            // Default route checking
            if ($routeName) {
                switch ($routeName) {
                    case 'login':
                        $action = 'LOGIN';
                        $description = 'Pengguna berhasil masuk ke sistem';
                        break;
                    case 'logout':
                        $action = 'LOGOUT';
                        $description = 'Pengguna keluar dari sistem';
                        break;
                    case 'attendance.store':
                        $action = 'ABSENSI';
                        $description = 'Melakukan absensi masuk/keluar harian';
                        break;
                    case 'attendance.permission.store':
                        $action = 'IZIN / SAKIT';
                        $description = 'Mengajukan surat izin / sakit';
                        break;
                    case 'leave.store':
                        $action = 'PENGAJUAN CUTI';
                        $description = 'Mengajukan permohonan cuti baru';
                        break;
                    case 'leave.approve':
                        $action = 'APPROVE CUTI';
                        $id = $request->route('id');
                        $description = "Menyetujui pengajuan cuti dengan ID: $id";
                        break;
                    case 'leave.reject':
                        $action = 'REJECT CUTI';
                        $id = $request->route('id');
                        $description = "Menolak pengajuan cuti dengan ID: $id";
                        break;
                    case 'user.store':
                        $action = 'ADD USER';
                        $description = 'Sistem merekam penambahan data karyawan baru';
                        break;
                    case 'user.update':
                        $action = 'UPDATE USER';
                        $nip = $request->route('nip');
                        $description = "Mengubah data karyawan dengan NIP: $nip";
                        break;
                    case 'user.destroy':
                        $action = 'DELETE USER';
                        $nip = $request->route('nip');
                        $description = "Menghapus data karyawan dengan NIP: $nip";
                        break;
                    case 'payroll.generate':
                        $action = 'GENERATE PAYROLL';
                        $id = $request->route('id');
                        $description = "Sistem melakukan proses perhitungan (generate) gaji untuk ID: $id";
                        break;
                    case 'payroll.destroy.detail':
                        $action = 'DELETE PAYROLL DETAIL';
                        $id = $request->route('id');
                        $description = "Menghapus rincian gaji karyawan dari daftar dengan ID Detail: $id";
                        break;
                    case 'superadmin.settings.update':
                        $action = 'UPDATE SETTINGS';
                        $description = 'Superadmin mengubah pengaturan sistem/global';
                        break;
                    case 'superadmin.payroll.lock':
                        $action = 'LOCK PAYROLL';
                        $id = $request->route('id');
                        $description = "Superadmin mengunci pembayaran gaji periode ini dengan ID: $id";
                        break;
                    case 'superadmin.payroll.unlock':
                        $action = 'UNLOCK PAYROLL';
                        $id = $request->route('id');
                        $description = "Superadmin membuka kunci pembayaran gaji periode ini dengan ID: $id";
                        break;
                    case 'role.store':
                    case 'division.store':
                    case 'departement.store':
                    case 'position.store':
                        $action = 'ADD MASTER DATA';
                        $description = 'Menambahkan data master (Role/Divisi/Dept/Jabatan) baru';
                        break;
                    case 'role.update':
                    case 'division.update':
                    case 'departement.update':
                    case 'position.update':
                        $action = 'UPDATE MASTER DATA';
                        $id = $request->route('id');
                        $description = "Mengubah data master dengan ID: $id";
                        break;
                    case 'role.destroy':
                    case 'division.destroy':
                    case 'departement.destroy':
                    case 'position.destroy':
                        $action = 'DELETE MASTER DATA';
                        $id = $request->route('id');
                        $description = "Menghapus data master dengan ID: $id";
                        break;
                }
            } else {
                // Fallback string matching using path for login/logout without proper route name (just in case)
                if (strpos($request->path(), 'login') !== false) {
                    $action = 'LOGIN';
                    $description = 'Pengguna berhasil masuk ke sistem';
                } elseif (strpos($request->path(), 'logout') !== false) {
                    $action = 'LOGOUT';
                    $description = 'Pengguna keluar dari sistem';
                }
            }

            try {
                ActivityLog::create([
                    'nip' => Auth::check() ? Auth::user()->nip : null,
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent(), 0, 255)
                ]);
            } catch (\Exception $e) {
                // Abaikan jika gagal log agar tidak merusak flow aplikasi
            }
        }

        return $response;
    }
}
