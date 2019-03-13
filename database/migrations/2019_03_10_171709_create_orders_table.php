<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', ['open', 'preparing', 'closed'])->default('open');
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->integer('waiter_id')->unsigned()->index()->nullable();
            $table->integer('sommelier_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('waiter_id')->references('id')->on('waiters');
            $table->foreign('sommelier_id')->references('id')->on('sommeliers');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wine_orders', function (Blueprint $table) {
            $table->enum('status', ['placed', 'delivered', 'unavailable'])->default('placed');
            $table->integer('order_id')->unsigned();
            $table->string('wine_guid');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('wine_guid')->references('guid')->on('wines');

            $table->index(['order_id', 'wine_guid']);
            $table->primary(['order_id', 'wine_guid']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wine_orders');
        Schema::dropIfExists('orders');
    }
}
