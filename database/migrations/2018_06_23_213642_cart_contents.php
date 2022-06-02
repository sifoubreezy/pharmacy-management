<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CartContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('post_id')->unsigned()->index();
            $table->integer('cart_id')->unsigned()->index();
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('cart_id')->references('id')->on('carts');
            $table->unique(['post_id','cart_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_contents');
    }
}
