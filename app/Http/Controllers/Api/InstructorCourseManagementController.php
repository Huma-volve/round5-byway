<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Http\Resources\Api\CourseResource;
use App\Http\Resources\Api\CourseCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InstructorCourseManagementController extends Controller
{
    /**
     * عرض جميع كورسات المدرس
     */
    public function index(Request $request)
    {
        // Get pagination parameters
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $courses = Course::where('user_id', Auth::id())
            ->with(['category', 'lessons'])
            ->withCount(['lessons', 'reviews', 'enrollments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => new CourseCollection($courses)
        ]);
    }

    /**
     * عرض كورس معين
     */
    public function show($id)
    {
        $course = Course::where('user_id', Auth::id())
            ->with(['category', 'lessons' => function ($query) {
                $query->orderBy('order');
            }])
            ->withCount(['lessons', 'reviews', 'enrollments'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new CourseResource($course)
        ]);
    }

    /**
     * إنشاء كورس جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:102400', // 100MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $course = new Course();
        $course->title = $request->title;
        $course->description = $request->description;
        $course->category_id = $request->category_id;
        $course->price = $request->price;
        $course->user_id = Auth::id();
        $course->status = 'draft';

        // رفع صورة الكورس
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses/images', 'public');
            $course->image_url = Storage::url($imagePath);
        }

        // رفع فيديو الكورس
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('courses/videos', 'public');
            $course->video_url = Storage::url($videoPath);
        }

        $course->save();

        return response()->json([
            'success' => true,
            'message' => 'The course has been created successfully.',
            'data' => $course->load('category')
        ], 201);
    }

    /**
     * تحديث كورس موجود
     */
    public function update(Request $request, $id)
    {
        $course = Course::where('user_id', Auth::id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:draft,pending,published',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $course->fill($request->only(['title', 'description', 'category_id', 'price', 'status']));

        // رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($course->image_url) {
                $oldImagePath = str_replace('/storage/', '', $course->image_url);
                Storage::disk('public')->delete($oldImagePath);
            }

            $imagePath = $request->file('image')->store('courses/images', 'public');
            $course->image_url = Storage::url($imagePath);
        }

        // رفع فيديو جديد
        if ($request->hasFile('video')) {
            // حذف الفيديو القديم
            if ($course->video_url) {
                $oldVideoPath = str_replace('/storage/', '', $course->video_url);
                Storage::disk('public')->delete($oldVideoPath);
            }

            $videoPath = $request->file('video')->store('courses/videos', 'public');
            $course->video_url = Storage::url($videoPath);
        }

        $course->save();

        return response()->json([
            'success' => true,
            'message' => 'The course has been updated successfully.',
            'data' => $course->load('category')
        ]);
    }

    /**
     * حذف كورس
     */
    public function destroy($id)
    {
        $course = Course::where('user_id', Auth::id())->findOrFail($id);

        // حذف الصورة والفيديو
        if ($course->image_url) {
            $imagePath = str_replace('/storage/', '', $course->image_url);
            Storage::disk('public')->delete($imagePath);
        }

        if ($course->video_url) {
            $videoPath = str_replace('/storage/', '', $course->video_url);
            Storage::disk('public')->delete($videoPath);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'The course has been successfully deleted.'
        ]);
    }


    /**
     * الحصول على الفئات المتاحة مع عدد الكورسات في كل فئة
     */
    public function getCategories()
    {
        $categories = Category::withCount('courses')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'courses_count' => $category->courses_count,
                    'created_at' => $category->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $category->updated_at?->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
            'total_categories' => $categories->count(),
            'total_courses' => $categories->sum('courses_count'),
        ]);
    }
}
