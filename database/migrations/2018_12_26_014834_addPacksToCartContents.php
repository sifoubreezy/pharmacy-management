<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPacksToCartContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_contents', function (Blueprint $table) {
            $table->unsignedInteger('post_id')->nullable()->change();
            $table->unsignedInteger('pack_id')->nullable();
            $table->enum('type',['post','pack'])->nullable();
            $table->foreign('pack_id')->references('id')->on('packs')->onDelete('cascade');
            $table->unique(['pack_id', 'cart_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_contents', function (Blueprint $table) {;
            $table->dropForeign('pack_id');
            $table->dropIndex(['pack_id', 'cart_id']);
            $table->dropColumn('post_id');
        });
    }
}
