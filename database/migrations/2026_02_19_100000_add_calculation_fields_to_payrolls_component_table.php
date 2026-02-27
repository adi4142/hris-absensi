<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalculationFieldsToPayrollsComponentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payrolls_component', function (Blueprint $table) {
            $table->string('calculation_type')->default('fixed')->after('type'); // fixed, percentage
            $table->decimal('calculation_value', 15, 2)->nullable()->after('calculation_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payrolls_component', function (Blueprint $table) {
            $table->dropColumn(['calculation_type', 'calculation_value']);
        });
    }
}
