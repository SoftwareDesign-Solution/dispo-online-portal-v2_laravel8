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
        Schema::create('user_projects', function (Blueprint $table) {

            // Id
            $table->id();

            // Knr
            $table->integer('Knr');

            // Ma_Knr
            $table->integer('Ma_Knr');

            // Projekt_Nr
            $table->integer('Projekt_Nr');

            // Tarif
            $table->char('Tarif', 1)->nullable(true)->default('B');

            // Freigabe_ind
            $table->tinyInteger('Freigabe_ind')->default(0);

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

            // Startorte
            $table->string('Startorte', 400)->nullable(true);

            // Heimatorte
            $table->string('Heimatorte', 400)->nullable(true);

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
        Schema::dropIfExists('user_projects');
    }
};
