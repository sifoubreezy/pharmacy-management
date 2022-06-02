<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixPurchaseAndPurchaseContent extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('purchase_content', function (Blueprint $table) {
////            $table->dropColumn('updated_price');
//            $table->dropColumn('status');
//            $table->dropColumn('payment_method');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('status')->default(1);
            $table->integer('payment_method')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('purchase_content', function (Blueprint $table) {
        });
    }
}
