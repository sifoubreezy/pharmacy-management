<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostTypeColumnToTablePacksPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packs_posts', function (Blueprint $table) {
            $table->enum('type',['obligatoire','bonus'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packs_posts', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
