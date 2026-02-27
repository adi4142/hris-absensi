<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceCodeToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Using string(8) for 5-8 digit code
            if (!Schema::hasColumn('employees', 'attendance_code')) {
                $table->string('attendance_code', 20)->nullable()->unique()->after('nip');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'attendance_code')) {
                $table->dropColumn('attendance_code');
            }
        });
    }
}
