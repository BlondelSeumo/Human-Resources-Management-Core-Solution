<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 160)->index();
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_default')->default(0);
            $table->foreignId('type_id')->nullable()->references('id')->on('types')->onDelete('SET NULL');
            $table->string('alias', 50)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('tenant_id')->default(1);
            $table->timestamps();

            $table->unique(['alias', 'tenant_id']);

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('type_id')->nullable()->constrained()->onDelete('NO ACTION');
            $table->string('name');
            $table->string('group_name')->nullable();
            $table->timestamps();

        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->text('meta')->nullable();

            $table->primary(['permission_id', 'role_id']);

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_permission');
        Schema::enableForeignKeyConstraints();
    }
}
