<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Helpers\ApiResponse;


class CourseShowController extends Controller
{
    /**
     * Get details of a specific course
     */
    public function show($id)
    {
        $course = Course::with([
            'user:id,name,about,image', // instructor
            'lessons:id,course_id,title,video_url',
            'reviews:user_id,course_id,rating,review,created_at',
            'reviews.user:id,name', // reviewer name
        ])->find($id);

        if (!$course) {
            return ApiResponse::sendError('Course not found.', 404);
        }

        // Format response
        return ApiResponse::sendResponse(200, 'Course details retrieved.', [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'price' => $course->price,
            'image' => $course->image,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,

            'instructor' => [
                'id' => $course->user->id,
                'name' => $course->user->name,
                'about' => $course->user->about ?? 'No bio available',
                'image' => $course->user->image ? url('storage/' . $course->user->image) : null,
            ],

            'content' => $course->lessons->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'video_url' => $lesson->video_url,
                ];
            }),

            'reviews' => $course->reviews->map(function ($review) {
                return [
                    'rating' => $review->rating,
                    'review' => $review->review,
                    'learner_name' => $review->user->name,
                    'created_at' => $review->created_at->diffForHumans(),
                ];
            }),

    
            'average_rating' => round($course->reviews->avg('rating'), 1),
        ]);
    }
}