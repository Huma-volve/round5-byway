<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Course;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // نجيب كل الكورسات
        $courses = Course::all();

        foreach ($courses as $course) {
            for ($i = 1; $i <= 5; $i++) {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => "Lesson $i for {$course->title}",
                    'description' => "شرح الدرس رقم $i في الكورس {$course->title}",
                    'duration' => rand(5, 20) . ':00', // مدة عشوائية من 5 لـ 20 دقيقة
                    'video_url' => "http://round5-byway.huma-volve.com/public/storage/videos
                    // /YOExFTSAv36jGfdiw6saruxtsoXAv7DpqyFWVKN7.mp4->id}_lesson{$i}.mp4",
                ]);
            }
        }
    }
}