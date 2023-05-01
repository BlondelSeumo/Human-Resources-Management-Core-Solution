<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_details', function (Blueprint $table) {
            $table->id();
            $table->dateTime('in_time');
            $table->dateTime('out_time')->nullable();
            $table->foreignId('attendance_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->foreignId('review_by')->nullable()->constrained('users', 'id');
            $table->foreignId('added_by')->nullable()->constrained('users', 'id');
            $table->foreignId('attendance_details_id')->nullable()->constrained();
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
        Schema::dropIfExists('attendance_details');
    }
}
