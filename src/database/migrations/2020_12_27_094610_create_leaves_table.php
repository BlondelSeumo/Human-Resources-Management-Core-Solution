<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('leave_type_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->foreignId('working_shift_details_id')->nullable()->constrained();
            $table->date('date')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('duration_type','191');
            $table->foreignId('assigned_by')->nullable()->constrained('users','id');
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
        Schema::dropIfExists('leaves');
    }
}
