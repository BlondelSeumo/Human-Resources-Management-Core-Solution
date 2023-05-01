<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentWorkingShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_working_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments', 'id');
            $table->foreignId('working_shift_id')->constrained('working_shifts', 'id');
            $table->date('start_date');
            $table->date('end_date')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_working_shift');
    }
}
