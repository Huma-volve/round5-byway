<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            ['title' => 'Web Development Basics', 'price' => 49.99,'user_id'=>1,'description'=>'lala','category_id'=>2],
            ['title' => 'UI/UX Design', 'price' => 39.99,'user_id'=>1,'description'=>'lala','category_id'=>2],
            ['title' => 'Python for Beginners', 'price' => 59.99,'user_id'=>1,'description'=>'lala','category_id'=>2],
            ['title' => 'Mobile App Development', 'price' => 69.99,'user_id'=>1,'description'=>'lala','category_id'=>2],
            ['title' => 'Data Science Intro', 'price' => 79.99,'user_id'=>1,'description'=>'lala','category_id'=>2],
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(
                ['title' => $course['title']],
                $course
            );
        }
    }
}
