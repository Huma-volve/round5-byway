<?php

namespace App\Http\Controllers;

use App\Models\Course;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // إنشاء كورس جديد
    public function store(Request $request)
    {
        $validatecourses = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'video'       => 'required|file|mimes:mp4,mov,avi|max:204800', // 200MB
            'status'      => 'required|in:published,unpublished',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);

        // رفع الفيديو على Cloudinary
        $uploadedFile = Cloudinary::uploadFile(
            $request->file('video')->getRealPath(),
            [
                'folder'        => 'courses_videos',
                'resource_type' => 'video'
            ]
        );
        $videoUrl = $uploadedFile->getSecurePath();

        // تخزين بيانات الكورس
        $course = Course::create([
            'user_id'     => auth()->id(),
            'title'       => $validatecourses['title'],
            'description' => $validatecourses['description'],
            'price'       => $validatecourses['price'],
            'category_id' => $validatecourses['category_id'],
            'status'      => $validatecourses['status'],
            'video_url'   => $videoUrl,
        ]);

        return ApiResponse::sendResponse(200, 'Course created successfully', $course);
    }

    // عرض قائمة الكورسات الخاصة بالمدرب
    public function listCourses(Request $request)
    {
        $instructorId = Auth::id();
        $query = Course::where('user_id', $instructorId);

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

        // فلترة بالتاريخ
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // الترتيب
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by; // price | rating
            $sortOrder = $request->get('sort_order', 'asc'); // asc | desc
            $query->orderBy($sortBy, $sortOrder);
        }

        $courses = $query->paginate(10);

        return response()->json($courses);
    }

    // تعديل الكورس
    public function update(Request $request, $id)
    {
        $course = Course::where('id', $id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        $course->update($request->only(['title', 'description', 'price', 'category_id']));

        return ApiResponse::sendResponse(200, 'Course updated successfully', $course);
    }

    // حذف الكورس
    public function destroy($id)
    {
        $course = Course::where('id', $id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        $course->delete();

        return ApiResponse::sendResponse(200, 'Course deleted successfully', $course);
    }
}
