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
        Schema::create('bookings', function (Blueprint $table) {

            // ID
            $table->id();

            // Knr
            $table->integer('Knr');

            // Ma_Knr
            $table->integer('Ma_Knr');

            // Vorschlagsdatum
            $table->date('Vorschlagsdatum')->nullable(true);

            // Honorar
            $table->decimal('Honorar', 4, 2);

            // Auslagen
            $table->decimal('Auslagen', 4, 2);

            // Typ
            $table->string('Type')->nullable(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
