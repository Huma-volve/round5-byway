<?php

namespace App\Http\Controllers;


use App\Helpers\ApiResponse;


use App\Models\Course;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CourseController extends Controller
{
//mycourses//
public function show(Course $course)
{
    $course->load('instructor'); // نحمل بيانات الانستراكتور

    return response()->json([
        'id' => $course->id,
        'title' => $course->title,
        'description' => $course->description,
        'image' => $course->image,
        'price' => $course->price,
        'rating' => $course->rating,
        'progress' => $course->progress, //  progress من الـ accessor
        'instructor' => $course->instructor->name, //  اسم الانستراكتور
        'lessons' => $course->lessons, // ممكن نرجع الدروس كمان
    ]);
}



    // إنشاء كورس جديد
    public function store(Request $request)
    {
        $validatecourses = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'video'       => 'required|file|mimes:mp4,mov,avi', // 200MB
            'status'      => 'required|in:published,unpublished',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);


        // لو فيه فيديو يترفع
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
        }
        // تخزين بيانات الكورس
        $course = Course::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'category_id' => $request->category_id,
            'status'      => $request->status,
            'video_url' => $videoPath
        ]);

        return ApiResponse::sendResponse(200, 'Course created successfully', $course);
    }


    // عرض قائمة الكورسات الخاصة بالمدرب
    public function listCourses(Request $request)
    {
        $instructorId = Auth::id();
        $query = Course::where('user_id', $instructorId)->latest();

        // البحث بالكلمة
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // فلترة بالتصنيف
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // فلترة بالتاريخ (من – إلى)

    // فلترة بالتاريخ
    if ($request->has('from_date') && $request->has('to_date')) {
        $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
    }
        // الترتيب


    $sortBy = $request->get('sort_by', 'price');   // price | rating
    $sortOrder = $request->get('sort_order', 'asc'); // asc | desc
    $query->orderBy($sortBy, $sortOrder);

    $courses = $query->paginate(10);


        $courses = $query->paginate(10);

        return response()->json($courses);
    }

    // تعديل الكورس
    public function update(Request $request, $id)
    {
$course = Course::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$course) {
            return ApiResponse::sendResponse(404 , 'Course not found');
        }
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'video_url' => 'nullable|string',
        ]);

        $course->update($validatedData);
        $course->refresh();

        return ApiResponse::sendResponse(200, 'Course updated successfully', $course);
    }


    // حذف الكورس
    public function destroy($id)
    {
        $course = Course::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        // التأكد إن اللي عامل auth هو صاحب الكورس

        if ($course->user_id !== auth()->id()) {
            return ApiResponse::sendResponse(
                403,
                'You are not the instructor of this course, so you cannot delete it.'
            );
        }

        // حذف الـ reviews المرتبطة
        if ($course->reviews()->count() > 0) {
            $course->reviews()->delete();
        }

        $course->delete();
        return ApiResponse::sendResponse(200, 'Course and its lessons deleted successfully');
    }
    // public function show($id)
    // {
    //     $course = Course::with(['instructor', 'lessons', 'reviews.user'])->findOrFail($id);
    //     return ApiResponse::sendResponse(200, 'Course details retrieved successfully', $course);
    // }
}
