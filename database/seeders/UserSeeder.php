<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@rapshop.pl',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+48123456789',
            'is_active' => true,
        ]);

        // Klienci testowi
        User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+48987654321',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Anna Nowak',
            'email' => 'anna@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+48555666777',
            'is_active' => true,
        ]);
    }
}