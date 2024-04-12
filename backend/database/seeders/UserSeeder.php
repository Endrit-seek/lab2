<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 
use Illuminate\Support\Str;



class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin123.')
        ]);

        User::create([
            'name' => 'Student',
            'email' => 'student@gmail.com',
            'password' => Hash::make('Student123.')
        ]);

        User::create([
            'name' => 'Instructor',
            'email' => 'isntructor@gmail.com',
            'password' => Hash::make('Instructor123.')
        ]);

        // Add three employee users
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => 'User' . ($i + 1),
                'email' => 'user' . ($i + 1) . '@example.com',
                'password' => Hash::make('User123.')
            ]);
        }
    }
}
