<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alias')->nullable();
            $table->enum('type', ['paid', 'unpaid', 'special']);
            $table->float('amount')->default(0.00);
            $table->float('special_percentage')->default(0.00);
            $table->boolean('is_enabled')->default(1);
            $table->boolean('is_earning_enabled')->default(0);
            $table->unsignedBigInteger('tenant_id');
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
        Schema::dropIfExists('leave_categories');
    }
}
