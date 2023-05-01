<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationAudiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_audiences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_setting_id');
            $table->string('audience_type');
            $table->text('audiences');
            $table->timestamps();

            $table->foreign('notification_setting_id')->references('id')->on('notification_settings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_audiences');
    }
}
