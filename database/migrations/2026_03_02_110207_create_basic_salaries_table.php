<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_salaries', function (Blueprint $table) {
            $table->id();
            $table->string('user_nip');
            $table->decimal('basic_salary', 15, 2);
            $table->date('effective_date');
            $table->timestamps();

            $table->foreign('user_nip')->references('nip')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basic_salaries');
    }
}
