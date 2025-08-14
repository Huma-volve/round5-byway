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
    public function index()
    {
        return $this->courseManagementServices->getAllCourses();
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
}
