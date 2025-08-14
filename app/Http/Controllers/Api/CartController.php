<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = Cart::with('course')->where('user_id', $request->user()->id)->get();
        return ApiResponse::sendResponse(200, 'Cart items retrieved successfully', $items);
    }

    public function add(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $cartItem = Cart::updateOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $request->course_id],
            []
        );

        return ApiResponse::sendResponse(201, 'Course added to cart successfully', $cartItem);
    }

    public function remove(Request $request, $courseId)
    {
        $cartItem = Cart::where([
            'user_id' => $request->user()->id,
            'course_id' => $courseId
        ])->first();

        if (!$cartItem) {
            return ApiResponse::sendResponse(200, 'Cart item not found');
        }

        $cartItem->delete();
        return ApiResponse::sendResponse(200, 'Course removed from cart successfully' ,[]);
    }
}
