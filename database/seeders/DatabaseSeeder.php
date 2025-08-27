<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Review;
use App\Models\Category;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\FavoritesCartSeeder;
use Illuminate\Database\Seeder;
use Database\Factories\ReviewFactory;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\FavoritesTableSeeder;
use Database\Seeders\InstructorProfileSeeder;
use Database\Seeders\PaymentSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            User::factory()->create([
                'role' => 'admin',
<<<<<<< Updated upstream
=======
            ],
            [
                'name' => 'Admin',
                'email' => 'admin1@gmail.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin2@gmail.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin3@gmail.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin4@gmail.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin5@gmail.com',
                'role' => 'admin',
            ],

        ];

        foreach ($users as $user) {
            User::firstOrCreate([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('12345678'),
                'role' => $user['role'],
>>>>>>> Stashed changes
                'status' => 'Active',
                'email' => "admin{$i}@gmail.com",
                'password' => Hash::make('12345678'),
            ]);
        }

        User::factory(10)->sequence(['role' => 'learner'], ['role' => 'instructor'])->create();
        $this->call([
            CategorySeeder::class,
        ]);
        Course::factory(10)->create();
        Lesson::factory(50)->create();
        Review::factory(50)->create();

        $this->call([
            InstructorProfileSeeder::class,
            UserSeeder::class,
            PaymentSeeder::class,
            CourseSeeder::class,
            FavoritesCartSeeder::class,
<<<<<<< Updated upstream
            NotificationSeeder::class,
            OrderSeeder::class,
            FavoritesCartSeeder::class,
=======
                LessonSeeder::class
>>>>>>> Stashed changes

        ]);

    }
}