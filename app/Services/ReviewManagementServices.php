<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\Review;

class ReviewManagementServices
{
    public function getAllReview()
    {
        $reviews = Review::with(['user:id,name', 'course:id,title'])
            ->get()
            ->map(function ($review) {
                return [
                    'course Name' => $review->course->title ?? null,
                    'reviewer' => $review->user->name ?? null,
                    'rating' => $review->rating,
                    'Comment Preview' => $review->review,
                    'Date' => $review->created_at,
                    'status' => $review->status,
                ];
            });

        return ApiResponse::sendResponse(200, 'All reviews retrieved successfully', $reviews);
    }

    public function showReview($reviewId)
    {
        $review = Review::where('id', $reviewId)->get();
        return ApiResponse::sendResponse();
    }
    
    public function deleteReview($reviewId)
    {
        Review::where('id', $reviewId)->delete();
        return ApiResponse::sendResponse();
    }
}
