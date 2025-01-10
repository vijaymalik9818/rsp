<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 60) as $index) { 
            Tour::create([
                'name'    => $faker->name, // Corrected method name to 'name'
                'phone'   => $faker->phoneNumber,
                'email'   => $faker->email,
                'time'    => $faker->time(), // Corrected method name to 'time' and added parentheses
                'date'    => $faker->date(),
                'message' => $faker->sentence(), // Corrected method name to 'sentence' and added parentheses
            ]);
        }
    }
}
