<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('value')->nullable();
            $table->string('settingable_type', 160)->nullable();
            $table->unsignedBigInteger('settingable_id')->nullable();
            $table->string('context')->nullable();
            $table->boolean('autoload')->default(0);
            $table->boolean('public')->default(1);
            $table->timestamps();

            $table->index(['settingable_type', 'settingable_id'], 'settingable_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
