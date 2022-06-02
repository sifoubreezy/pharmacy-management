<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Columns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ville')->nullable();
            $table->unsignedInteger('cod_postal')->nullable();
            $table->unsignedInteger('R_C')->nullable();
            $table->unsignedInteger('I_F')->nullable();
            $table->unsignedInteger('A_I')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ville');
            $table->dropColumn('cod_postal');
            $table->dropColumn('R_C');
            $table->dropColumn('I_F');
            $table->dropColumn('A_I');
        });
    }
}
