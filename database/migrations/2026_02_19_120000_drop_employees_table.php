<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropEmployeesTableSecond extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('employees');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Since we merged data into users, we don't easily restore this table without data loss
        // But for safety, we define the structure
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
            $table->string('nip', 25)->nullable()->unique();
            $table->string('name', 100);
            $table->unsignedBigInteger('user_id');
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->unsignedBigInteger('departement_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('division_id');
            $table->text('address');
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('attendance_code', 20)->nullable()->unique();
            $table->timestamps();
        });
    }
}
