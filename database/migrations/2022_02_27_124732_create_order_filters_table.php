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
        Schema::create('order_filters', function (Blueprint $table) {

            // ID
            $table->id();

            // Knr
            $table->integer('Knr');

            // Projekt_Knr
            $table->integer('Projekt_Knr');

            // PaketNr
            $table->integer('PaketNr')->nullable(true);

            // Auftragsdatum
            $table->date('Auftragsdatum')->nullable(true);

            // Ab_Ort
            $table->string('Ab_Ort', 25);

            // Ab_Zeit
            $table->time('Ab_Zeit');

            // An_Ort
            $table->string('An_Ort', 25)->nullable(true);

            // An_Zeit
            $table->time('An_Zeit')->nullable(true);

            // Bs1_ind
            $table->tinyInteger('Bs1_ind')->default(0);

            // Bs2_ind
            $table->tinyInteger('Bs2_ind')->default(0);

            // Bs5_ind
            $table->tinyInteger('Bs5_ind')->default(0);

            // Bs6_ind
            $table->tinyInteger('Bs6_ind')->default(0);

            // Bs7_ind
            $table->tinyInteger('Bs7_ind')->default(0);

            // Bs8_ind
            $table->tinyInteger('Bs8_ind')->default(0);

            // Bs9_ind
            $table->tinyInteger('Bs9_ind')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_filters');
    }
};
