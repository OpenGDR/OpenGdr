<?php

namespace Database\Seeders;

use App\Models\Race;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Race::create([
            'name' => 'Razza 1',
            'slug' => 'razza-1',
            'visible' => 1,
            'open' => 1,
            'enable_for_registration' => 1,
        ]);

        Race::create([
            'name' => 'Razza 2',
            'slug' => 'razza-2',
            'visible' => 1,
            'open' => 1,
            'enable_for_registration' => 1,
        ]);

        Race::create([
            'name' => 'Razza 3',
            'slug' => 'razza-3',
            'visible' => 1,
            'open' => 1,
            'enable_for_registration' => 1,
        ]);
    }
}
