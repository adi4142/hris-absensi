<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLeaveSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('annual_allowance')->default(12);
            $table->boolean('can_carry_over')->default(false);
            $table->integer('max_days_per_request')->default(12);
            $table->timestamps();
        });

        // Insert default setting
        DB::table('leave_settings')->insert([
            'annual_allowance' => 12,
            'can_carry_over' => false,
            'max_days_per_request' => 12,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_settings');
    }
}
