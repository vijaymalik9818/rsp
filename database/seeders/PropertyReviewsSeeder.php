<?php

namespace Database\Seeders;

use App\Models\PropertyReview;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PropertyReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 30) as $index) { 
            PropertyReview::create([
                'review_from' => $faker->name,
                'title'       => $faker->sentence,
                'rating'      => $faker->numberBetween(1, 5),
                'review'      => $faker->paragraph,
                'slug'        => $faker->slug,
            ]);
        }
    }
}
