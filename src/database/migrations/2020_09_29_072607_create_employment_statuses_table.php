<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmploymentStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class');
            $table->longText('description')->nullable();
            $table->boolean('is_default')->default(0);
            $table->string('alias', 30)->nullable();
            $table->unsignedBigInteger('tenant_id');

            $table->unique(['alias', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employment_statuses');
    }
}
