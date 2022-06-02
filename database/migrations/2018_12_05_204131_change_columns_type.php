<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsType extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('R_C')->nullable()->change();
            $table->bigInteger('I_F')->nullable()->change();
            $table->bigInteger('A_I')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('R_C')->change();
            $table->unsignedInteger('I_F')->change();
            $table->unsignedInteger('A_I')->change();
        });
    }
}
