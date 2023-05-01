<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('value');
            $table->string('contextable_type');
            $table->string('contextable_id');
            $table->unsignedBigInteger('custom_field_id');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('custom_field_id')
                ->references('id')
                ->on('custom_fields');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_field_values');
    }
}
