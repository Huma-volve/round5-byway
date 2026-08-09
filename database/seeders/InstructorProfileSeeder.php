<?php

namespace Database\Seeders;

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InstructorProfileSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = [
            [
                'name' => 'أحمد محمد',
                'email' => 'ahmed.instructor@example.com',
                'bio' => 'مدرس محترف في مجال تطوير الويب مع خبرة أكثر من 5 سنوات. متخصص في Laravel و React.',
                'total_earnings' => 2500.00,
                'twitter_link' => 'https://twitter.com/ahmed_web',
                'linkdin_link' => 'https://linkedin.com/in/ahmed-web',
                'youtube_link' => 'https://youtube.com/@ahmed_web_dev',
                'facebook_link' => 'https://facebook.com/ahmed.web.dev',
            ],
            [
                'name' => 'سارة أحمد',
                'email' => 'sara.instructor@example.com',
                'bio' => 'مدرسة في مجال التصميم الجرافيكي والرسوم المتحركة. أعمل في المجال منذ 3 سنوات.',
                'total_earnings' => 1800.50,
                'twitter_link' => 'https://twitter.com/sara_design',
                'linkdin_link' => 'https://linkedin.com/in/sara-design',
                'youtube_link' => 'https://youtube.com/@sara_design',
                'facebook_link' => 'https://facebook.com/sara.design',
            ],
            [
                'name' => 'محمد علي',
                'email' => 'mohamed.instructor@example.com',
                'bio' => 'مدرس في مجال الذكاء الاصطناعي والتعلم الآلي. حاصل على ماجستير في علوم الحاسوب.',
                'total_earnings' => 3200.75,
                'twitter_link' => 'https://twitter.com/mohamed_ai',
                'linkdin_link' => 'https://linkedin.com/in/mohamed-ai',
                'youtube_link' => 'https://youtube.com/@mohamed_ai',
                'facebook_link' => 'https://facebook.com/mohamed.ai',
            ],
            [
                'name' => 'فاطمة حسن',
                'email' => 'fatima.instructor@example.com',
                'bio' => 'مدرسة في مجال التسويق الرقمي وإدارة وسائل التواصل الاجتماعي. خبرة 4 سنوات في المجال.',
                'total_earnings' => 2100.25,
                'twitter_link' => 'https://twitter.com/fatima_marketing',
                'linkdin_link' => 'https://linkedin.com/in/fatima-marketing',
                'youtube_link' => 'https://youtube.com/@fatima_marketing',
                'facebook_link' => 'https://facebook.com/fatima.marketing',
            ],
            [
                'name' => 'علي محمود',
                'email' => 'ali.instructor@example.com',
                'bio' => 'مدرس في مجال تطوير تطبيقات الموبايل. متخصص في Flutter و React Native.',
                'total_earnings' => 2800.00,
                'twitter_link' => 'https://twitter.com/ali_mobile',
                'linkdin_link' => 'https://linkedin.com/in/ali-mobile',
                'youtube_link' => 'https://youtube.com/@ali_mobile_dev',
                'facebook_link' => 'https://facebook.com/ali.mobile.dev',
            ],
        ];

        foreach ($instructors as $instructorData) {
            // إنشاء المستخدم أولاً
            $user = User::create([
                'name' => $instructorData['name'],
                'email' => $instructorData['email'],
                'password' => Hash::make('12345678'),
                'role' => 'instructor',
                'status' => 'Active',
            ]);

            // إنشاء ملف المدرس
            InstructorProfile::create([
                'user_id' => $user->id,
                'bio' => $instructorData['bio'],
                'total_earnings' => $instructorData['total_earnings'],
                'twitter_link' => $instructorData['twitter_link'],
                'linkdin_link' => $instructorData['linkdin_link'],
                'youtube_link' => $instructorData['youtube_link'],
                'facebook_link' => $instructorData['facebook_link'],
            ]);
        }


        // إنشاء مدرسين إضافيين باستخدام Factory إذا كان موجود
        if (class_exists('App\Models\InstructorProfile')) {
            // يمكن إضافة factory هنا إذا كان موجود
            // InstructorProfile::factory(5)->create();
        }

        InstructorProfile::create([
            'user_id'        => $user->id,
            // 'name'           => $user->name,
            'bio'            => 'This is a sample instructor bio.',
            'total_earnings' => 1200.50,
            'twitter_link'   => 'https://twitter.com/test',
            'linkdin_link'   => 'https://linkedin.com/in/test',
            'youtube_link'   => 'https://youtube.com/@test',
            'facebook_link'  => 'https://facebook.com/test',
            // 'image'          => null,

                'first_name' => 'Osamannn',
                'last_name'  => 'El-Greidy',
                'headline'   => 'Full Stack Developer',
                'about'      => 'I love teaching and coding about Laravel, React, and Odoo.',
                'skills'     => ['Laravel', 'React', 'Odoo', 'MySQL', 'API Development'],
                'work_experiences' => [
                    [
                        'job_title'    => 'Backend Developer',
                        'company_name' => 'TechCorp',
                        'start_date'   => '2022-01-01',
                        'end_date'     => '2023-06-01',
                    ],
                    [
                        'job_title'    => 'Full Stack Developer',
                        'company_name' => 'CodeWay',
                        'start_date'   => '2023-07-01',
                        'end_date'     => null,
                    ]
                ],
            ]
        );


    }
}
