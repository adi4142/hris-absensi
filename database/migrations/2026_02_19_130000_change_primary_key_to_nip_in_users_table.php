<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangePrimaryKeyToNipInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Drop existing foreign keys that point to 'employees' which are now dangling
        Schema::table('attendances', function (Blueprint $table) {
            // Check if constraint exists before dropping
            try {
                $table->dropForeign('attendances_employee_nip_foreign');
            } catch (\Exception $e) {}
        });

        Schema::table('payrolls_detail', function (Blueprint $table) {
            try {
                $table->dropForeign('payrolls_detail_nip_foreign');
            } catch (\Exception $e) {}
        });

        // 2. Change users table primary key
        // We use raw SQL for some parts because dropping AI PK in MySQL is tricky with Blueprint
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Remove auto_increment first
        DB::statement('ALTER TABLE users MODIFY user_id BIGINT UNSIGNED NOT NULL');
        // Drop primary key
        DB::statement('ALTER TABLE users DROP PRIMARY KEY');
        
        // Make NIP primary
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 25)->nullable(false)->change();
            $table->primary('nip');
            $table->dropColumn('user_id');
        });

        // 3. Re-add foreign keys pointing to 'users(nip)'
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('employee_nip')->references('nip')->on('users')->onDelete('cascade');
        });

        Schema::table('payrolls_detail', function (Blueprint $table) {
            $table->foreign('nip')->references('nip')->on('users')->onDelete('cascade');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['employee_nip']);
        });

        Schema::table('payrolls_detail', function (Blueprint $table) {
            $table->dropForeign(['nip']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary(['nip']);
            $table->bigIncrements('user_id')->first();
            $table->string('nip', 25)->nullable()->change();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
