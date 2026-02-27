<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateAttendanceStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Menambahkan status 'Permission' dan 'Alpha' ke dalam enum status
        // Menggunakan DB::statement karena Laravel Blueprint tidak mendukung perubahan ENUM secara native dengan mudah
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('Present', 'Late', 'Excused', 'Sick', 'Permission', 'Alpha') DEFAULT 'Present'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('Present', 'Late', 'Excused', 'Sick') DEFAULT 'Present'");
    }
}
