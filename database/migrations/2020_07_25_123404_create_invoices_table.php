<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('invoices', static function (Blueprint $table) {

            $table->increments('id');
            $table->integer('ref_invoice_id');
            $table->integer('post_id');
            $table->date('date_perm');
            $table->string('com_name', 50);
            $table->integer('pv_ht');
            $table->integer('ppa');
            $table->integer('quantity');
            $table->text('offre')->nullable();
            $table->string('tag', 20)->nullable();
            $table->string('Conditionnement', 80)->nullable();
            $table->integer('cart_id')->nullable();
            $table->string('image');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
}
