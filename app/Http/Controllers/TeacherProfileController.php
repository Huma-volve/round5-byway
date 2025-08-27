<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\InstructorProfile;
use Illuminate\Http\Request;

class TeacherProfileController extends Controller
{

    public function show()
    {
        $teacher = InstructorProfile::where('user_id', auth()->id())
            ->with('user')
            ->first();

        if (!$teacher) {
            return ApiResponse::sendResponse(404, 'Teacher profile not found');
        }

        return ApiResponse::sendResponse(200, 'Teacher profile found successfully', $teacher);
    }

    // update profile
    public function update(Request $request)
    {
        $data = $request->validate([
            'bio'            => 'sometimes|string|max:1000',
        'headline'   => 'nullable|string|max:255',
        'about'      => 'nullable|string',
        'skills'     => 'nullable|array',
        'work_experience' => 'nullable|array',
            'name'           => 'sometimes|string|max:255',
            'twitter_link'   => 'nullable|url',
            'linkdin_link'   => 'nullable|url',
            'youtube_link'   => 'nullable|url',
            'facebook_link'  => 'nullable|url',
        ]);

        $user = auth()->user();
        $instructor = InstructorProfile::where('user_id', $user->id)->first();

        if (!$instructor) {
            return ApiResponse::sendResponse(404, 'Profile not found');
        }

        if (array_key_exists('name', $data)) {
            $user->name = $data['name'];
            $user->save();
            unset($data['name']);
        }

        if (!empty($data)) {
            $instructor->update($data);
        }

        $instructor->load('user');

        return ApiResponse::sendResponse(200, 'Profile updated successfully', $instructor);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'headline'   => 'nullable|string|max:255',
            'about'      => 'nullable|string',
            'skills'     => 'nullable|array',
            'work_experiences' => 'nullable|array',
            'bio'            => 'nullable|string|max:1000',
            'twitter_link'   => 'nullable|url',
            'linkdin_link'   => 'nullable|url',
            'youtube_link'   => 'nullable|url',
            'facebook_link'  => 'nullable|url',
        ]);

        $userId = auth()->id();

        $existing = InstructorProfile::where('user_id', $userId)->first();
        if ($existing) {
            return ApiResponse::sendResponse(409, 'Profile already exists', $existing);
        }

        $validated['user_id'] = $userId;

$profile = InstructorProfile::updateOrCreate(
    ['user_id' => $userId], // الشرط
    $validated              // القيم
)->load('user');

        return ApiResponse::sendResponse(201, 'Profile created successfully', $profile);
    }
}