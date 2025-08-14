<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReviewManagementServices;
use Illuminate\Http\Request;

class ReviewManagementController extends Controller
{
    public function __construct(
        protected ReviewManagementServices $reviewService
    ) {}
    // كل الريفيوهات
    public function index()
    {
        return $this->reviewService->getAllReview();
    }

    // عرض ريفيو واحد
    public function show($id)
    {
        return $this->reviewService->showReview($id);
    }

    // حذف ريفيو
    public function destroy($id)
    {
        return $this->reviewService->deleteReview($id);
    }
}
