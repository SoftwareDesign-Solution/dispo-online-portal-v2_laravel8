<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_cart_items', function (Blueprint $table) {

            // id
            $table->id();

            // ordercart_id
            //$table->unsignedBigInteger('ordercart_id');

            $table->integer('Knr');

            $table->integer('PaketNr')->nullable(true);

            $table->date('Vorschlagsdatum')->nullable(true);

            $table->decimal('Auslagen', 19, 2)->nullable(true);

            $table->decimal('Honorar', 19, 6)->nullable(true)->default(0);

            $table->string('Type')->nullable(true);

            $table->timestamps();

            $table->foreignId('ordercart_id')->constrained('order_carts')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_cart_items');
    }
};
