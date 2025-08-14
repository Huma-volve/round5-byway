<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\UserManagementServices;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function __construct(
        protected UserManagementServices $userManagementServices
    ) {}
    // عرض كل المستخدمين
    public function index()
    {
        return $this->userManagementServices->allRegisteredUsers();
    }
    // عرض اليوزر بروفايل
    public function show($id)
    {
        return $this->userManagementServices->userProfile($id);
    }

    public function toggleStatus($id)
    {
        return $this->userManagementServices->toggleUserStatus($id);
    }
    // حذف المستخدمين
    public function destroy($id)
    {
        return $this->userManagementServices->deleteUser($id);
    }
    // البحث عن مستخدم
    public function searchUsers(Request $request)
    {
        $key = $request->query('key');
        return $this->userManagementServices->searchUsers($key);
    }

    public function updateUser(Request $request, $userId)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'role' => ['sometimes', 'string', Rule::in(['learner', 'instructor'])],
        ]);

        return $this->userManagementServices->updateUser($userId, $validated);
    }
    // عرض كل الاساتذة
    public function allInstructors()
    {
        return $this->userManagementServices->allInstructors();
    }
}
