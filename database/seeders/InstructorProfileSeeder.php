<?php

namespace Database\Seeders;

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstructorProfileSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // أو ممكن تعمل User جديد لو مش موجود
        if (!$user) {
            $user = User::create([
                'name' => 'Test Instructor',
                'email' => 'test@instructor.com',
                'password' => bcrypt('password'),
            ]);
        }

        InstructorProfile::create([
            'user_id'        => $user->id,
            'name'           => $user->name,
            'bio'            => 'This is a sample instructor bio.',
            'total_earnings' => 1200.50,
            'twitter_link'   => 'https://twitter.com/test',
            'linkdin_link'   => 'https://linkedin.com/in/test',
            'youtube_link'   => 'https://youtube.com/@test',
            'facebook_link'  => 'https://facebook.com/test',
            'image'          => null,
        ]);
    }
}
