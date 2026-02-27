<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MergeEmployeesIntoUsersTable extends Migration
{
    public function up()
    {
        // 1. Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 25)->nullable()->unique()->after('user_id');
            $table->string('phone', 20)->nullable()->after('email');
            $table->unsignedBigInteger('departement_id')->nullable()->after('phone');
            $table->unsignedBigInteger('position_id')->nullable()->after('departement_id');
            $table->unsignedBigInteger('division_id')->nullable()->after('position_id');
            $table->text('address')->nullable()->after('division_id');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('gender', ['Male', 'Female'])->nullable()->after('date_of_birth');
            $table->string('attendance_code', 20)->nullable()->unique()->after('gender');
        });

        // 2. Add Superadmin role if not exists
        DB::table('roles')->insertOrIgnore([
            ['name' => 'superadmin', 'created_at' => now(), 'updated_at' => now()]
        ]);
        
        // 3. Migrate data from employees to users
        $employees = DB::table('employees')->get();
        foreach ($employees as $employee) {
            DB::table('users')
                ->where('user_id', $employee->user_id)
                ->update([
                    'nip' => $employee->nip,
                    'phone' => $employee->phone,
                    'departement_id' => $employee->departement_id,
                    'position_id' => $employee->position_id,
                    'division_id' => $employee->division_id,
                    'address' => $employee->address,
                    'date_of_birth' => $employee->date_of_birth,
                    'gender' => $employee->gender,
                    'attendance_code' => $employee->attendance_code,
                ]);
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nip', 'phone', 'departement_id', 'position_id', 
                'division_id', 'address', 'date_of_birth', 
                'gender', 'attendance_code'
            ]);
        });
    }
}
