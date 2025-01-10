<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;

class LeadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $leads = [
            [
                'id' => 12,
                'name' => 'deepak',
                'email' => 'deepak@peregrine-it.com',
                'password' => bcrypt('secret'),
                'phone' => '123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];
        foreach ($leads as $leadData) {
            Lead::create($leadData);
        }
    }
}
