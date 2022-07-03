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
        Schema::create('commissions', function (Blueprint $table) {

            // Id
            $table->id();

            // Projekt_Knr
            $table->integer('Projekt_Knr');

            // Ma_Knr_Von
            $table->integer('Ma_Knr_Von');

            // Ma_Knr_Bis
            $table->integer('Ma_Knr_Bis');

            // TeilnetzNr
            $table->string('TeilnetzNr', 10);

            // Bs
            $table->string('Bs', 20);

            // Schichttag
            $table->string('Schichttag', 100);

            // Tarif
            $table->char('Tarif', 1)->nullable(true)->default('B');

            // Zeit_von
            $table->time('Zeit_von');

            // Zeit_bis
            $table->time('Zeit_bis');

            // PreisEinzel
            $table->decimal('PreisEinzel', 19, 6)->nullable(true)->default(0);

            // PreisKette
            $table->decimal('PreisKette', 19, 6)->nullable(true)->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commissions');
    }
};
