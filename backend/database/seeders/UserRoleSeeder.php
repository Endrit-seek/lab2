<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Retrieve roles
         $adminRole = Role::where('name', 'admin')->first();
         $instructorRole = Role::where('name', 'instructor')->first();
         $studentRole = Role::where('name', 'student')->first();
         $guestRole = Role::where('name', 'guest')->first();
 
         // Assign admin role to a specific user (e.g., user with ID 1)
         $adminUser = User::find(1); // Change 1 to the ID of the user you want to assign as admin
         if ($adminUser && $adminRole) {
             $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
         }
 
         // Assign student role to some users (e.g., users with IDs 2, 3, 4, 5, 6)
         $instructorUserIds = [2, 3, 4, 5, 6]; // Replace with the IDs of the users you want to assign as student
         foreach ($instructorUserIds as $userId) {
             $studentUser = User::find($userId);
             if ($studentUser && $studentRole) {
                 $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);
             }
         }

         // Assign instructor role to some users (e.g., users with IDs 2, 3, 4)
         $instructorUserIds = [2, 3, 4]; // Replace with the IDs of the users you want to assign as instructor
         foreach ($instructorUserIds as $userId) {
             $instructorUser = User::find($userId);
             if ($instructorUser && $instructorRole) {
                 $instructorUser->roles()->syncWithoutDetaching([$instructorRole->id]);
             }
         }
 
         // Assign guest role to all users except the admin
         $allUsers = User::where('id', '!=', 1)->get(); // Exclude the admin user (ID 1)
         foreach ($allUsers as $user) {
             if ($guestRole) {
                 $user->roles()->syncWithoutDetaching([$guestRole->id]);
             }
         }
    }
}
