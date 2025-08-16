<?php

namespace App\Http\Controllers\Api\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Cart;
use App\Models\favorite;

class CourseInteractionController extends Controller
{
    // 🔹 Add to Cart (No Quantity)
public function addToCart(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id'
    ]);

    $userId = $request->user()->id;
    $courseId = $request->course_id;

    // Check if already in cart
    $exists = Cart::where('user_id', $userId)
        ->where('course_id', $courseId)
        ->exists();

    if ($exists) {
        return ApiResponse::sendResponse(200, 'Course already in cart.');
    }

    // Add to cart
    Cart::create([
        'user_id' => $userId,
        'course_id' => $courseId,
    ]);

    return ApiResponse::sendResponse(201, 'Course added to cart.');
}

// 🔹 Remove from Cart
public function removeFromCart(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id'
    ]);

    $deleted = Cart::where('user_id', $request->user()->id)
        ->where('course_id', $request->course_id)
        ->delete();

    return ApiResponse::sendResponse(200, $deleted
        ? 'Course removed from cart.'
        : 'Course not in cart.');
}

// 🔹 Get Cart
public function getCart()
{
    $cartItems = Cart::where('user_id', request()->user()->id)
        ->with('course') // Make sure Course model exists
        ->get();

    return ApiResponse::sendResponse(200, 'Cart retrieved.', $cartItems);
}

/**
     * Add course to favorites
     */
    public function addToFavorites(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $userId = $request->user()->id;
        $courseId = $request->course_id;

        $exists = Favorite::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();

        if ($exists) {
            return ApiResponse::sendResponse(200, 'Course already in favorites.');
        }

        Favorite::create([
            'user_id' => $userId,
            'course_id' => $courseId,
        ]);

        return ApiResponse::sendResponse(201, 'Course added to favorites.');
    }

    /**
     * Remove course from favorites
     */
    public function removeFromFavorites(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $userId = $request->user()->id;
        $courseId = $request->course_id;

        $deleted = Favorite::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->delete();

        return ApiResponse::sendResponse(200, $deleted
            ? 'Course removed from favorites.'
            : 'Course not in favorites.');
    }

    /**
     * Get user's favorite courses
     */
    public function getFavorites()
    {
        $userId = request()->user()->id;

        $favorites = Favorite::where('user_id', $userId)
            ->with('course') // Load course data
            ->get();

        return ApiResponse::sendResponse(200, 'Favorites retrieved.', $favorites);
    }
}
