<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payruns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uid');
            $table->foreignId('status_id')->constrained();
            $table->text('data');
            $table->enum('followed', ['employee','settings','customized'])->default('settings');
            $table->string('batch_id',255)->nullable();
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
        Schema::dropIfExists('payruns');
    }
}
