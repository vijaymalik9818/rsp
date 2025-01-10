<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JoinRep;
use Faker\Factory as Faker;

class JoinRepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 60) as $index) { 
            JoinRep::create([
                'first_name'    => $faker->firstName,
                'last_name'     => $faker->lastName,
                'phone'         => $faker->phoneNumber,
                'email'         => $faker->email,
                'joinee'        => $faker->boolean,

                'experience'    => $faker->sentence,
                'licensed_area' => $faker->sentence,
                'practice_areas' => $faker->sentence,
                'reference'      => $faker->sentence,
                'about'          => $faker->paragraph,
                'is_contact'     => $faker->boolean,
                'perceive'      => $faker->sentence,
                'join_rep_data' => json_encode([
                    'data' => $faker->sentence,
                ]),
            ]);
        }
    }
}
