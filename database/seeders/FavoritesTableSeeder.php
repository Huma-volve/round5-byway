<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoritesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get user IDs
        $omnya = DB::table('users')->where('email', 'omnya@example.com')->value('id');
        $john = DB::table('users')->where('email', 'john@example.com')->value('id');
        $alice = DB::table('users')->where('email', 'alice@example.com')->value('id');

        // Get course IDs
        $uiux = DB::table('courses')->where('title', 'UI/UX Design Fundamentals')->value('id');
        $figma = DB::table('courses')->where('title', 'Advanced Figma Techniques')->value('id');
        $js = DB::table('courses')->where('title', 'JavaScript Essentials')->value('id');
        $react = DB::table('courses')->where('title', 'React for Beginners')->value('id');

        // Insert enrollments (favorites = enrollment)
        DB::table('favorites')->insert([
            // Omnya enrolled in UI/UX & Figma
            ['user_id' => $omnya, 'course_id' => $uiux],
            ['user_id' => $omnya, 'course_id' => $figma],

            // John enrolled in UI/UX & JS
            ['user_id' => $john, 'course_id' => $uiux],
            ['user_id' => $john, 'course_id' => $js],

            // Alice enrolled in React
            ['user_id' => $alice, 'course_id' => $react],
        ]);
    }
}
