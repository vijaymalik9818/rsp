<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ContactUsSeeder;
use Database\Seeders\JoinRepSeeder;
use Database\Seeders\PropertyReviewsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed the ContactUs table
     //   $this->call(ContactUsSeeder::class);

        // Seed the JoinRep table
     //   $this->call(JoinRepSeeder::class);
 
      //  seed the property_reviews table
      //  $this->call(PropertyReviewsSeeder::class);

      // seed the tour table
        $this->call(TourSeeder::class);

        // You can add more seeders here if needed
        // $this->call(AdditionalSeeder::class);
    }
}

?>