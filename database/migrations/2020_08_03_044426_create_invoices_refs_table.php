<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesRefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices_refs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provider_id');
            $table->integer('remise')->nullable();
            $table->integer('num_invoice');
            $table->integer('total_h_t');
            $table->integer('total_net');
            $table->date('created_date');
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
        Schema::dropIfExists('invoices_refs');
    }
}
