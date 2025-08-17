<?php

namespace App\Http\Controllers\Api\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Review;
use App\Helpers\ApiResponse;

class CourseProgressController extends Controller
{
    /**
     * Mark a lesson as completed
     */
    public function completeLesson(Request $request, $lessonId)
    {
        $lesson = Lesson::with('course')->find($lessonId);

        if (!$lesson) {
            return ApiResponse::sendError('Lesson not found.', 404);
        }

        $userId = $request->user()->id;
        $courseId = $lesson->course->id;

        // Check if user is enrolled
        $isEnrolled = Enrollment::where('learner_id', $userId)
            ->where('course_id', $courseId)
            ->exists();

        if (!$isEnrolled) {
            return ApiResponse::sendError('You are not enrolled in this course.', 403);
        }

        // Check if already completed
        $alreadyCompleted = LessonCompletion::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->exists();

        if ($alreadyCompleted) {
            return ApiResponse::sendResponse(200, 'Lesson already marked as completed.');
        }

        // Mark as completed
        LessonCompletion::create([
            'user_id' => $userId,
            'lesson_id' => $lessonId,
        ]);

        return ApiResponse::sendResponse(200, 'Lesson marked as completed.');
    }

    /**
     * Submit a course review (evaluation)
     */
    public function submitReview(Request $request, $courseId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000'
        ]);

        $course = Course::find($courseId);

        if (!$course) {
            return ApiResponse::sendError('Course not found.', 404);
        }

        $userId = $request->user()->id;

        // Check enrollment
        $isEnrolled = Enrollment::where('learner_id', $userId)
            ->where('course_id', $courseId)
            ->exists();

        if (!$isEnrolled) {
            return ApiResponse::sendError('You must be enrolled to review this course.', 403);
        }

        // Check if already reviewed
        $existingReview = Review::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($existingReview) {
            return ApiResponse::sendError('You have already reviewed this course.', 400);
        }

        // Save review
        Review::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return ApiResponse::sendResponse(201, 'Thank you for your review!');
    }
}
