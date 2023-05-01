<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkingShiftDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_shift_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('weekday')->comment('shorthand lowercase (sun/mon)');
            $table->foreignId('working_shift_id')->constrained();
            $table->boolean('is_weekend')->default(0);
            $table->time('start_at')->nullable();
            $table->time('end_at')->nullable();
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
        Schema::dropIfExists('working_shift_details');
    }
}
