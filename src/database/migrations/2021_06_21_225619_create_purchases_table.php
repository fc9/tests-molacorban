<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_uuid')->nullable(false);
            $table->foreignId('supplier_id');
            $table->foreignId('product_id');
            $table->foreignId('customer_id');
            $table->float('value', 8, 2);
            $table->float('rate', 8, 2);
            $table->tinyInteger('status');
            $table->timestamp('date');
            $table->timestamp('returned_at')->nullable();
            $table->softDeletes();

            $table->foreign('batch_uuid')->references('uuid')->on('batches');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
