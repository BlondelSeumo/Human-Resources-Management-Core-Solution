<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayslipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->foreignId('payrun_id')->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->double('net_salary');
            $table->double('basic_salary');
            $table->string('period');
            $table->boolean('consider_overtime')->default(1);
            $table->enum('consider_type', ['hour', 'daily_log', 'none'])->default('none');
            $table->boolean('without_beneficiary')->default(0);
            $table->unsignedBigInteger('tenant_id')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payslips');
    }
}
