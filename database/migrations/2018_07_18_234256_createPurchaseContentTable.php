<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_content', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_id')->unsigned()->index();
            $table->integer('post_id')->unsigned()->index();
            $table->unsignedInteger('quantity');
            $table->unsignedDecimal('unit_price');
            $table->unsignedDecimal('price');

            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->unique(['post_id','purchase_id']);
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
        Schema::dropIfExists('purchase_content');
    }
}
