<?php

namespace Database\Seeders;

use App\Models\ContactUs;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ContactUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 60) as $index) { 
            ContactUs::create([
                'first_name'    => $faker->firstName,
                'last_name'     => $faker->lastName,
                'phone'         => $faker->phoneNumber,
                'email'         => $faker->email,
                'time'          => $faker->dateTime,
                'comment'       => $faker->sentence,
                'contact_data'  => json_encode([$faker->phoneNumber]),
            ]);
        }
    }
}
