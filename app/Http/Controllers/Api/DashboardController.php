<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService as ServicesDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected ServicesDashboardService $dashboardService
    ) {}

    public function getDashboardStatistics()
    {
        return $this->dashboardService->getDashboardStatistics();
    }
    public function getTopRatedCourses()
    {
        return $this->dashboardService->getTopRatedCourses();
    }
}
