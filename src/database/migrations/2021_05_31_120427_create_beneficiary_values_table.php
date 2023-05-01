<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiary_values', function (Blueprint $table) {
            $table->id();
            $table->morphs('beneficiary_valuable','bv');
            $table->foreignId('beneficiary_id')->constrained();
            $table->double('amount');
            $table->boolean('is_percentage')->default(0);
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
        Schema::dropIfExists('beneficiary_values');
    }
}
