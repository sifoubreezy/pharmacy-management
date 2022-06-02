<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPacksToPurchaseContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_content', function (Blueprint $table) {


            $table->unsignedInteger('post_id')->nullable()->change();
            $table->unsignedInteger('pack_id')->nullable();
            $table->enum('type',['post','pack'])->nullable();
            $table->foreign('pack_id')->references('id')->on('packs')->onDelete('cascade');
            $table->dropForeign('purchase_content_post_id_foreign');
            $table->dropForeign('purchase_content_purchase_id_foreign');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_content', function (Blueprint $table) {
            //
        });
    }
}
