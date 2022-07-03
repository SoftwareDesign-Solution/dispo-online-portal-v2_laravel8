<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        User::create([
            'knr' => -1,
            'idnr' => 'makue',
            'anrede' => 'Herr',
            'vorname' => 'Manuel',
            'nachname' => 'Kübler',
            'v_ende' => 0,
            'email' => 'mail@softwaredesign-solution.de',
            'email_ind' => 0,
            'zaehlung_rank' => 0,
            'befragung_rank' => 0,
            'freigabe_ind' => 1,
            'password' => bcrypt('M!len@17'),
            'Admin' => true
        ]);
    }
}
