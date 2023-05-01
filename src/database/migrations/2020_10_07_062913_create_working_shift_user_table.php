<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkingShiftUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_shift_user', function (Blueprint $table) {
            $table->foreignId('working_shift_id')->constrained();
            $table->foreignId('user_id')->constrained();
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
        Schema::dropIfExists('working_shift_user');
    }
}
