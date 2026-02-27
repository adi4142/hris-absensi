<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\JobAplicantController;
use App\Http\Controllers\JobVacancieController;
use App\Http\Controllers\SelectionApplicantController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\RoleVerificationController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Authentication Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration & Verification Routes Removed (Admin managed accounts)

// Role Verification Routes (Needs Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/role/verify', [RoleVerificationController::class, 'show'])->name('role.verification.notice');
    Route::post('/role/verify', [RoleVerificationController::class, 'verify'])->name('role.verification.verify');
});

// Password Reset Routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/verify', [ForgotPasswordController::class, 'showCodeForm'])->name('password.verify.show');
Route::post('/password/verify', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
Route::get('/password/reset/new', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/password/reset/new', [ForgotPasswordController::class, 'reset'])->name('password.update');

Route::middleware(['auth', 'role.access'])->group(function () {
    Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');
    Route::post('/chat', [ChatbotController::class, 'chat'])->name('chat');
    

    Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
    Route::post('/role', [RoleController::class, 'store'])->name('role.store');
    Route::get('/role/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
    Route::put('/role/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('/role/{id}', [RoleController::class, 'destroy'])->name('role.destroy');

    Route::get('/division', [DivisionController::class, 'index'])->name('division.index');
    Route::get('/division/create', [DivisionController::class, 'create'])->name('division.create');
    Route::post('/division', [DivisionController::class, 'store'])->name('division.store');
    Route::get('/division/{id}/edit', [DivisionController::class, 'edit'])->name('division.edit');
    Route::put('/division/{id}', [DivisionController::class, 'update'])->name('division.update');
    Route::delete('/division/{id}', [DivisionController::class, 'destroy'])->name('division.destroy');

    Route::get('/departement', [DepartementController::class, 'index'])->name('departement.index');
    Route::get('/departement/create', [DepartementController::class, 'create'])->name('departement.create');
    Route::post('/departement', [DepartementController::class, 'store'])->name('departement.store');
    Route::get('/departement/{id}/edit', [DepartementController::class, 'edit'])->name('departement.edit');
    Route::put('/departement/{id}', [DepartementController::class, 'update'])->name('departement.update');
    Route::delete('/departement/{id}', [DepartementController::class, 'destroy'])->name('departement.destroy');

    Route::get('/position', [PositionController::class, 'index'])->name('position.index');
    Route::get('/position/create', [PositionController::class, 'create'])->name('position.create');
    Route::post('/position', [PositionController::class, 'store'])->name('position.store');
    Route::get('/position/{id}/edit', [PositionController::class, 'edit'])->name('position.edit');
    Route::put('/position/{id}', [PositionController::class, 'update'])->name('position.update');
    Route::delete('/position/{id}', [PositionController::class, 'destroy'])->name('position.destroy');

    Route::get('/user', [UsersController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UsersController::class, 'create'])->name('user.create');
    Route::post('/user', [UsersController::class, 'store'])->name('user.store');
    Route::get('/user/{nip}/edit', [UsersController::class, 'edit'])->name('user.edit');
    Route::put('/user/{nip}', [UsersController::class, 'update'])->name('user.update');
    Route::delete('/user/{nip}', [UsersController::class, 'destroy'])->name('user.destroy');

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
    Route::get('/payroll/{id}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::get('/payroll/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
    Route::put('/payroll/{id}', [PayrollController::class, 'update'])->name('payroll.update');
    Route::delete('/payroll/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    Route::delete('/payroll/detail/{id}', [PayrollController::class, 'destroyDetail'])->name('payroll.destroy.detail');
    Route::post('/payroll/{id}/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
    Route::get('/payroll/detail/{id}', [PayrollController::class, 'showDetail'])->name('payroll.detail');
    Route::get('/payroll/download/{id}', [PayrollController::class, 'downloadPdf'])->name('payroll.download');
    Route::post('/payroll/detail/{id}/add-component', [PayrollController::class, 'addComponent'])->name('payroll.addComponent');
    Route::post('/payroll/detail/{id}/update-basic', [PayrollController::class, 'updateBasicSalary'])->name('payroll.updateBasic');
    Route::delete('/payroll/detail/{id}/delete-component/{component_id}', [PayrollController::class, 'deleteComponent'])->name('payroll.deleteComponent');
    Route::get('/payroll/detail/{id}/edit-component/{component_id}', [PayrollController::class, 'editComponent'])->name('payroll.editComponent');
    Route::post('/payroll/detail/{id}/update-component/{component_id}', [PayrollController::class, 'updateComponent'])->name('payroll.updateComponent');

    // Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index'); // Disabled - Merged to User
    // ... other employee routes removed

    Route::get('/selection', [SelectionController::class, 'index'])->name('selection.index');
    Route::get('/selection/create', [SelectionController::class, 'create'])->name('selection.create');
    Route::post('/selection', [SelectionController::class, 'store'])->name('selection.store');
    Route::get('/selection/{id}/edit', [SelectionController::class, 'edit'])->name('selection.edit');
    Route::put('/selection/{id}', [SelectionController::class, 'update'])->name('selection.update');
    Route::delete('/selection/{id}', [SelectionController::class, 'destroy'])->name('selection.destroy');

    Route::get('/jobapplicant', [JobAplicantController::class, 'index'])->name('jobapplicant.index');
    Route::get('/jobapplicant/create', [JobAplicantController::class, 'create'])->name('jobapplicant.create');
    Route::post('/jobapplicant', [JobAplicantController::class, 'store'])->name('jobapplicant.store');
    Route::get('/jobapplicant/{id}/edit', [JobAplicantController::class, 'edit'])->name('jobapplicant.edit');
    Route::put('/jobapplicant/{id}', [JobAplicantController::class, 'update'])->name('jobapplicant.update');
    Route::delete('/jobapplicant/{id}', [JobAplicantController::class, 'destroy'])->name('jobapplicant.destroy');

    Route::get('/jobvacancie', [JobVacancieController::class, 'index'])->name('jobvacancie.index');
    Route::get('/jobvacancie/create', [JobVacancieController::class, 'create'])->name('jobvacancie.create');
    Route::post('/jobvacancie', [JobVacancieController::class, 'store'])->name('jobvacancie.store');
    Route::get('/jobvacancie/{id}/edit', [JobVacancieController::class, 'edit'])->name('jobvacancie.edit');
    Route::put('/jobvacancie/{id}', [JobVacancieController::class, 'update'])->name('jobvacancie.update');
    Route::delete('/jobvacancie/{id}', [JobVacancieController::class, 'destroy'])->name('jobvacancie.destroy');

    Route::get('/selectionapplicant', [SelectionApplicantController::class, 'index'])->name('selectionapplicant.index');
    Route::get('/selectionapplicant/create', [SelectionApplicantController::class, 'create'])->name('selectionapplicant.create');
    Route::post('/selectionapplicant', [SelectionApplicantController::class, 'store'])->name('selectionapplicant.store');
    Route::get('/selectionapplicant/{id}/edit', [SelectionApplicantController::class, 'edit'])->name('selectionapplicant.edit');
    Route::put('/selectionapplicant/{id}', [SelectionApplicantController::class, 'update'])->name('selectionapplicant.update');
    Route::delete('/selectionapplicant/{id}', [SelectionApplicantController::class, 'destroy'])->name('selectionapplicant.destroy');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/detail', [AttendanceController::class, 'absensi'])->name('attendance.detail');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/dashboard', [AttendanceController::class, 'dashboard'])->name('attendance.dashboard');
    Route::get('/attendance/permission/create', [AttendanceController::class, 'createPermission'])->name('attendance.permission.create');
    Route::post('/attendance/permission', [AttendanceController::class, 'storePermission'])->name('attendance.permission.store');
    Route::get('/attendance/monitoring', [AttendanceController::class, 'monitoring'])->name('attendance.monitoring');
    Route::get('/attendance/history/{id}', [AttendanceController::class, 'employeeHistory'])->name('attendance.employeeHistory');

    Route::get('/jobapplication', [JobApplicationController::class, 'index'])->name('jobapplication.index');
    Route::put('/jobapplication/{id}', [JobApplicationController::class, 'update'])->name('jobapplication.update');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Fitur Cuti
    Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [LeaveController::class, 'create'])->name('leave.create');
    Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');
    
    // HRD Special Routes for Leave
    Route::post('/leave/{id}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{id}/reject', [LeaveController::class, 'reject'])->name('leave.reject');


    // Superadmin Special Routes
    Route::middleware(['role.superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        // System Settings
        Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');
        
        // Payroll Locking
        Route::post('/payroll/{id}/lock', [PayrollController::class, 'lock'])->name('payroll.lock');
        Route::post('/payroll/{id}/unlock', [PayrollController::class, 'unlock'])->name('payroll.unlock');

        // Leave Settings
        Route::get('/leave-settings', [LeaveSettingController::class, 'index'])->name('leave_settings.index');
        Route::post('/leave-settings', [LeaveSettingController::class, 'update'])->name('leave_settings.update');
    });
});

// Public Routes for Attendance Scanning
Route::get('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance/shortcut-permission', [AttendanceController::class, 'shortcutPermission'])->name('attendance.shortcutPermission');




