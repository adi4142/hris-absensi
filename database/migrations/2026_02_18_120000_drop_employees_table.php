<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropEmployeesTable extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // 1. Drop existing FKs if we can identify them (optional if disabling constraints)
        // 2. Drop the employees table
        Schema::dropIfExists('employees');

        // 3. Re-add foreign keys pointing to users table
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['employee_nip']);
            $table->foreign('employee_nip')->references('nip')->on('users')->onDelete('cascade');
        });

        Schema::table('payrolls_detail', function (Blueprint $table) {
            $table->dropForeign(['nip']);
            $table->foreign('nip')->references('nip')->on('users')->onDelete('cascade');
        });

        Schema::table('employees_leaves', function (Blueprint $table) {
            $table->dropForeign(['nip']);
            $table->foreign('nip')->references('nip')->on('users')->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
    }
}
