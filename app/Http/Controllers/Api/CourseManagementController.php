<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseManagementServices;
use Illuminate\Http\Request;

class CourseManagementController extends Controller
{
    public function __construct(
        protected CourseManagementServices $courseManagementServices
    ) {}
    //  عرض كل الكورسات
    public function index(Request $request)
    {
        // جمع الفلاتر من الريكوست
        $filters = $request->only(['category_id', 'status']);

        return $this->courseManagementServices->getAllCourses($filters);
    }
    // حذف كورس
    public function destroy($courseId)
    {
        return $this->courseManagementServices->deleteCourse($courseId);
    }
    // الموافقة على كورس
    public function approve($courseId)
    {
        return $this->courseManagementServices->approveCourse($courseId);
    }
    // تعديل بيانات الكورس
    public function update(Request $request, $courseId)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'video_url'   => 'sometimes|url',
            'status'      => 'sometimes|in:published,pending',
        ]);

        return $this->courseManagementServices->updateCourse($courseId, $validated);
    }
    // عرض كورس محدد
    public function show($courseId)
    {
        return $this->courseManagementServices->getCourseById($courseId);
    }
    // البحث عن كورس
    public function search(Request $request)
    {
        $filters = $request->query('key');
        return $this->courseManagementServices->searchCourses($filters);
    }
}
