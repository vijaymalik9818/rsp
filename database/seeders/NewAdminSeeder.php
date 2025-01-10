<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'no-reply@infrontmarketing.ca',
            'name' => 'Admin',
            'phone' => '123456789',
            'password' => Hash::make('admin@repinc.ca'),
            'role' => 1,
            'slug_url' => 'Admin'
        ]);
    }
}
