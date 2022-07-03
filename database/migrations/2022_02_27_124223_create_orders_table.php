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
        Schema::create('orders', function (Blueprint $table) {

            // ID
            $table->id();

            // Knr
            $table->integer('Knr');

            // Projekt_Knr
            $table->integer('Projekt_Knr');

            // Projekt
            $table->string('Projekt', 20);

            // FahrtNr
            $table->string('FahrtNr', 50);

            // Auftragsdatum
            $table->date('Auftragsdatum')->nullable(true);

            // Schichttag
            $table->string('Schichttag', 15);

            // Wochentag
            $table->string('Wochentag', 15)->nullable(true);

            // Ab_Ort
            $table->string('Ab_Ort', 25);

            // Ab_Zeit
            $table->time('Ab_Zeit');

            // An_Ort
            $table->string('An_Ort', 25)->nullable(true);

            // An_Zeit
            $table->time('An_Zeit')->nullable(true);

            // Bs
            $table->string('Bs', 2);

            // AnzahlPers
            $table->integer('AnzahlPers');

            // TeilnetzNr
            $table->string('TeilnetzNr', 10);

            // AG9
            $table->string('AG9', 50);

            // Ab_mm
            $table->integer('Ab_mm');

            // An_mm
            $table->integer('An_mm')->nullable(true);

            // EpVorschlag_dat
            $table->date('EpVorschlag_dat')->nullable(true);

            // SysBemerkung
            $table->string('SysBemerkung', 100)->nullable(true);

            // PaketNr
            $table->integer('PaketNr')->nullable(true);

            // OrdNr
            $table->integer('OrdNr')->nullable(true);

            // Dauer
            $table->integer('Dauer')->nullable(true);

            // Auslagen
            $table->decimal('Auslagen', 4, 2)->nullable(true);

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
        Schema::dropIfExists('orders');
    }
};
