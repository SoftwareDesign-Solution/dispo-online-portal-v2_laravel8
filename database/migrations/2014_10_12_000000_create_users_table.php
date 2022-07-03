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
        Schema::create('users', function (Blueprint $table) {

            $table->id();
            $table->integer('knr');
            $table->string('idnr', 20)->unique();
            $table->string('anrede', 20);
            $table->string('nachname', 40);
            $table->string('vorname', 40);
            $table->integer('v_ende');
            $table->string('email')->nullable(true);
            $table->tinyInteger('email_ind')->default(0);
            $table->integer('zaehlung_rank')->default(0);
            $table->integer('befragung_rank')->default(0);
            $table->tinyInteger('freigabe_ind')->default(0);
            $table->integer('FirstLogin')->default(1);
            $table->boolean('verified')->default(false);
            $table->boolean('Admin')->default(false);
            $table->boolean('Api')->default(false);

            //$table->string('name');
            //$table->string('email')->unique();
            //$table->timestamp('email_verified_at')->nullable();

            $table->timestamp('password_change_at')->nullable();

            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
