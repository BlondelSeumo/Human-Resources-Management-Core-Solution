<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAttendanceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_details', function (Blueprint $table) {
            $table->text('in_ip_data')->after('attendance_id')->nullable();
            $table->text('out_ip_data')->after('in_ip_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_details', function (Blueprint $table) {
            $table->dropColumn('in_ip_data');
            $table->dropColumn('out_ip_data');
        });
    }
}
