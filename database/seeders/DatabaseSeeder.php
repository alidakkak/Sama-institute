<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\InOutLog;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => '00000000',
        ]);

//        Student::create([
//            'first_name' => 'Ali',
//            'last_name' => 'Dakkak',
//            'age' => '22',
//            'date_of_birth' => '2002-3-3',
//            'place_of_birth' => 'Damascus',
//            'gender' => 'male',
//            'previous_educational_status' => 'ds',
//            'phone_number' => '0937356470',
//            'location' => 'ad',
//            'password' => '00000000',
//            'image' => '/students_image/female.jpg',
//        ]);
    }
}
