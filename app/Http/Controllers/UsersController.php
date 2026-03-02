<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Departement;
use App\Position;
use App\Division;
use App\LeaveSetting;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (strtolower(auth()->user()->role->name ?? '') !== 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat membuat akun baru.');
        }

        $roles = Role::all();
        $departements = Departement::all();
        $positions = Position::all();
        $divisions = Division::all();
        return view('user.create', compact('roles', 'departements', 'positions', 'divisions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentUserRole = strtolower(auth()->user()->role->name ?? '');
        if ($currentUserRole !== 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat membuat akun baru.');
        }

        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users'],
            'password' => ['required','string','min:8'],
            'roles_id' => ['required','exists:roles,roles_id'],
            'nip' => ['required','string','max:25','unique:users'],
            'phone' => ['required','string','max:20'],
            'departement_id' => 'required|exists:departements,departement_id',
            'position_id' => 'required|exists:positions,position_id',
            'division_id' => 'required|exists:divisions,division_id',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
        ],[
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'roles_id.required' => 'Role wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'departement_id.required' => 'Departemen wajib diisi.',
            'departement_id.exists' => 'Departemen tidak valid.',
            'position_id.required' => 'Posisi wajib diisi.',
            'position_id.exists' => 'Posisi tidak valid.',
            'division_id.required' => 'Divisi wajib diisi.',
            'division_id.exists' => 'Divisi tidak valid.',
            'gender.required' => 'Jenis kelamin wajib diisi.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date' => 'Tanggal lahir tidak valid.',
            'date_of_birth.date' => 'Tanggal lahir tidak valid.',
        ]);

        // Security Check: Non-Superadmin cannot create Superadmin or HRD
        $newRole = Role::find($request->roles_id);
        $newRoleName = strtolower($newRole->name ?? '');
        if ($currentUserRole !== 'superadmin' && ($newRoleName === 'superadmin' || $newRoleName === 'hrd' || $newRoleName === 'admin')) {
            return abort(403, 'Hanya Superadmin yang bisa menunjuk Superadmin baru atau HRD baru.');
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'roles_id' => $request->roles_id,
            'nip' => $request->nip,
            'phone' => $request->phone,
            'departement_id' => $request->departement_id,
            'position_id' => $request->position_id,
            'division_id' => $request->division_id,
            'address' => $request->address,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'email_verified_at' => now(), // Auto-verify accounts created by admin
            'is_role_verified' => true,    // Auto-verify roles
        ];

        // Ambil jatah cuti default dari pengaturan
        $leaveSetting = LeaveSetting::first();
        $defaultLeave = $leaveSetting ? $leaveSetting->annual_allowance : 12;
        $data['total_jatah_cuti'] = $defaultLeave;
        $data['sisa_cuti'] = $defaultLeave;
        $data['cuti_terpakai'] = 0;

        // Auto-generate attendance code for karyawan if nip is provided
        if ($request->nip) {
            do {
                $attendanceCode = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            } while (User::where('attendance_code', $attendanceCode)->exists());
            $data['attendance_code'] = $attendanceCode;
        }

        User::create($data);

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($nip)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($nip)
    {
        $currentUserRole = strtolower(auth()->user()->role->name ?? '');
        if ($currentUserRole !== 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat mengedit data pengguna.');
        }

        $edituser = User::findOrFail($nip);
        $roles = Role::all();
        $departements = Departement::all();
        $positions = Position::all();
        $divisions = Division::all();

        return view('user.edit', compact('edituser', 'roles', 'departements', 'positions', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $nip)
    {
        $currentUserRole = strtolower(auth()->user()->role->name ?? '');
        if ($currentUserRole !== 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat memperbarui data pengguna.');
        }

        $user = User::findOrFail($nip);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$nip.',nip',
            'password' => 'nullable|string|min:8',
            'roles_id' => 'required|exists:roles,roles_id',
            'nip' => 'required|string|max:25|unique:users,nip,'.$nip.',nip',
            'phone' => 'nullable|string|max:20',
            'departement_id' => 'nullable|exists:departements,departement_id',
            'position_id' => 'nullable|exists:positions,position_id',
            'division_id' => 'nullable|exists:divisions,division_id',
            'gender' => 'nullable|in:Male,Female',
            'date_of_birth' => 'nullable|date',
        ]);

        // 2. Security Check: Non-Superadmin cannot promote someone to Superadmin or HRD
        $newRole = Role::find($request->roles_id);
        $newRoleName = strtolower($newRole->name ?? '');
        if ($currentUserRole !== 'superadmin' && ($newRoleName === 'superadmin' || $newRoleName === 'hrd' || $newRoleName === 'admin')) {
            return abort(403, 'Hanya Superadmin yang bisa mengangkat Superadmin baru atau HRD baru.');
        }
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'roles_id' => $request->roles_id,
            'nip' => $request->nip,
            'phone' => $request->phone,
            'departement_id' => $request->departement_id,
            'position_id' => $request->position_id,
            'division_id' => $request->division_id,
            'address' => $request->address,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ];

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        // Generate attendance code if it was null and now has NIP
        if ($request->nip && !$user->attendance_code) {
            do {
                $attendanceCode = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            } while (User::where('attendance_code', $attendanceCode)->exists());
            $data['attendance_code'] = $attendanceCode;
        }

        $user->update($data);
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($nip)
    {
        $currentUserRole = strtolower(auth()->user()->role->name ?? '');
        if ($currentUserRole !== 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat menghapus akun pengguna.');
        }

        $user = User::findOrFail($nip);

        $user->delete();
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
