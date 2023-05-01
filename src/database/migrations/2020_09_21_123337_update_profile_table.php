<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('gender');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('joining_date')->after('date_of_birth')->nullable();
            $table->string('employee_id',160)->after('joining_date')->nullable();
            $table->string('marital_status')->after('employee_id')->nullable();
            $table->string('fathers_name')->after('marital_status')->nullable();
            $table->string('mothers_name')->after('fathers_name')->nullable();
            $table->string('social_security_number')->after('mothers_name')->nullable();
            $table->text('about_me')->after('social_security_number')->nullable();
            $table->string('phone_number',255)->after('employee_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->dropForeign(['designation_id']);
            $table->dropColumn('designation_id');
            $table->dropColumn('joining_date');
            $table->dropColumn('employee_id');
            $table->dropColumn('marital_status');
            $table->dropColumn('fathers_name');
            $table->dropColumn('mothers_name');
            $table->dropColumn('social_security_number');
        });
    }
}
