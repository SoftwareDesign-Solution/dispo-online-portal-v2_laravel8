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
        Schema::create('shift_days', function (Blueprint $table) {

            // ID
            $table->id();

            // Projekt_Knr
            $table->integer('Projekt_Nr');

            // Projekt
            $table->string('Projekt', 20);

            // TeilnetzNr
            $table->string('TeilnetzNr', 10);

            // Schichttag
            $table->string('Schichttag', 15);

            // Datum
            $table->date('Datum');

            //$table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_days');
    }
};
