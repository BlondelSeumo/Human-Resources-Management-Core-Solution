<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrunSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrun_settings', function (Blueprint $table) {
            $table->id();
            $table->morphs('payrun_settingable','ps');
            $table->string('payrun_period');
            $table->string('consider_type');
            $table->boolean('consider_overtime')->default(1);
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
        Schema::dropIfExists('payrun_settings');
    }
}
