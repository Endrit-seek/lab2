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
            'name' => 'User',
            'email' => 'user@gmail.com',
            'email_verified_at' => now(), 
            'password' => Hash::make('User123.'), 
            'remember_token' => Str::random(10), 
            'created_at' => now(), 
            'updated_at' => now(), 
        ]);

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => 'User ' . ($i + 1),
                'email' => 'user' . ($i + 1) . '@example.com',
                'email_verified_at' => now(), 
                'password' => Hash::make('password'), 
                'remember_token' => Str::random(10), 
                'created_at' => now(), 
                'updated_at' => now(), 
            ]);
        }
    }
}
