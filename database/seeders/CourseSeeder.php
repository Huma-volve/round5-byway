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
        // نفترض عندك user_id = 1 و category_id = 1 (غيّرهم حسب البيانات اللي عندك)
        $instructorId = 1;

        Course::create([
            'user_id' => $instructorId,
            'title' => 'Laravel Basics',
            'description' => 'Introduction to Laravel framework.',
            'video_url' => 'https://example.com/videos/laravel-basics.mp4',
            'status' => 'published',
            'price' => 100,
            'category_id' => 1,
        ]);

        Course::create([
            'user_id' => $instructorId,
            'title' => 'Advanced React',
            'description' => 'Deep dive into React concepts.',
            'video_url' => 'https://example.com/videos/advanced-react.mp4',
            'status' => 'pending',
            'price' => 200,
            'category_id' => 2,
        ]);

        Course::create([
            'user_id' => $instructorId,
            'title' => 'Mobile Development with Flutter',
            'description' => 'Learn to build cross-platform mobile apps.',
            'video_url' => 'http://round5-byway.huma-volve.com/public/storage/
            // videos/YOExFTSAv36jGfdiw6saruxtsoXAv7DpqyFWVKN7.mp4',
            'status' => 'published',
            'price' => 150,
            'category_id' => 3,
        ]);
    }
}
