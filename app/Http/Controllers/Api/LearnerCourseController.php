<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;

class LearnerCourseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'learner') {
            return ApiResponse::sendError('Unauthorized. Only learners can access this.', 403);
        }

        $courses = DB::table('favorites')
            ->join('courses', 'favorites.course_id', '=', 'courses.id')
            ->join('users as instructors', 'courses.user_id', '=', 'instructors.id')
            ->where('favorites.user_id', $user->id)
            ->select(
                'courses.id as course_id',
                'courses.title',
                'instructors.name as instructor'
            )
            ->get()
            ->map(function ($course) use ($user) {
                // Count total lessons
                $totalLessons = DB::table('lessons')
                    ->where('course_id', $course->course_id)
                    ->count();

                // Count completed lessons
                $completedLessons = DB::table('lesson_completions')
                    ->where('learner_id', $user->id)
                    ->whereIn('lesson_id', function ($q) use ($course) {
                        $q->select('id')
                          ->from('lessons')
                          ->where('course_id', $course->course_id);
                    })
                    ->count();

                $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

                return [
                    'course_id' => $course->course_id,
                    'title' => $course->title,
                    'instructor' => $course->instructor,
                    'progress' => $progress . '%',
                ];
            });

        return ApiResponse::sendResponse(200, 'Enrolled courses retrieved successfully.', [
            'courses' => $courses,
            'count' => $courses->count(),
        ]);
    }
}
