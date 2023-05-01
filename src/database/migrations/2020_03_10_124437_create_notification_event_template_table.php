<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationEventTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_event_template', function (Blueprint $table) {
            $table->foreignId('notification_event_id')->constrained()->onDelete('cascade');
            $table->foreignId('notification_template_id')
                ->references('id')
                ->on('notification_templates')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_event_template');
    }
}
