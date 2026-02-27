<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_jatah_cuti')->default(12)->after('basic_salary');
            $table->integer('cuti_terpakai')->default(0)->after('total_jatah_cuti');
            $table->integer('sisa_cuti')->default(12)->after('cuti_terpakai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_jatah_cuti', 'cuti_terpakai', 'sisa_cuti']);
        });
    }
}
