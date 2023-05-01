<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_id')->constrained();
            $table->foreignId('reviewed_by')->constrained('users', 'id');
            $table->foreignId('status_id')->constrained();
            $table->foreignId('department_id')->nullable()->constrained();
            $table->boolean('is_last')->default(1);
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
        Schema::dropIfExists('leave_statuses');
    }
}
