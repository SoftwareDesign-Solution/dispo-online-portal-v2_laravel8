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
        Schema::create('projects', function (Blueprint $table) {

            $table->id();

            // Projekt_Nr
            $table->integer('Projekt_Nr');

            // Projekt_Nr
            $table->string('Projekt');

            // Delta
            $table->integer('Delta');

            // Delta_Vorlauf
            $table->integer('Delta_Vorlauf');

            // Datumsvorschlag
            $table->boolean('Datumsvorschlag');

            // ObergrenzeAuslagen
            $table->integer('ObergrenzeAuslagen');

            // ObergrenzeAuslagenEP
            $table->integer('ObergrenzeAuslagenEP');

            // Haltestellenquelle
            $table->string('Haltestellenquelle');

            // ProjektlaufzeitVon
            $table->date('ProjektlaufzeitVon');

            // ProjektlaufzeitBis
            $table->date('ProjektlaufzeitBis');

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
        Schema::dropIfExists('projects');
    }
};
