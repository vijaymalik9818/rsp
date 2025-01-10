<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'email' => 'admin@admin.com',
            'name' => 'admin',
            'phone' => '123456789',
            'password' => Hash::make('admin@password'),
            'role' => 1
        ]);
    }
}
