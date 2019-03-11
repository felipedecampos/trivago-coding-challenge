<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wines', function (Blueprint $table) {
            $table->string('guid', 255)->unique();
            $table->string('variety');
            $table->string('region');
            $table->year('year');
            $table->float('price', 8, 2);
            $table->longText('link');
            $table->dateTimeTz('pub_date');

            $table->timestamps();

            $table->softDeletes();

            $table->primary('guid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wines');
    }
}
