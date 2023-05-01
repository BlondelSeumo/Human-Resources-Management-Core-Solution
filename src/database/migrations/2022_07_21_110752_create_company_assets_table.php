<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->string('serial_number')->unique()->nullable();
            $table->foreignId('type_id')->constrained('company_asset_types');
            $table->enum('is_working', ['yes', 'no', 'maintenance']);
            $table->date('date')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('company_assets');
    }
}
