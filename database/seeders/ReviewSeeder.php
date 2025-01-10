<?php

namespace Database\Seeders;
use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Review::create([
            'review_from' => '24',
            'review_to' => '11',
            'review_feedback' => 'this is a test review',
            'rating' => '3',
            'avg_rating' =>'3', 
        ]);
    }
}
