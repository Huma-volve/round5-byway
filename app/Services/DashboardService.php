<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\User;
use App\Models\Course;


class DashboardService
{
    public function getDashboardStatistics()
    {
        $data = [
            'active_learners' => User::where([
                ['role', '=', 'learner'],
                ['status', '=', 'Active']
            ])->count(),

            'active_instructors' => User::where([
                ['role', '=', 'instructor'],
                ['status', '=', 'Active']
            ])->count(),

            'published_courses' => Course::where('status', 'published')->count(),
        ];
        return ApiResponse::sendResponse(200, 'Dashboard statistics retrieved successfully', $data);
    }

    public function getTopRatedCourses()
    {
        $courses = Course::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with('instructor:id,name')
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get(['id', 'title', 'instructor_id'])
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'average_rating' => round($course->reviews_avg_rating ?? 0, 2),
                    'reviews_count' => $course->reviews_count ?? 0,
                    'instructor_name' => $course->instructor->name ?? null,
                ];
            });

        return ApiResponse::sendResponse(200, 'Top rated courses retrieved successfully', $courses);
    }
}
