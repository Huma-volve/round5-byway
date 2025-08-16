<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Get the authenticated learner's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'learner') {
            return ApiResponse::sendError('Unauthorized access.', 403);
        }

        return ApiResponse::sendResponse(200, 'Profile fetched successfully.', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'headline' => $user->headline,
                'about' => $user->about,
                'twitter_link' => $user->twitter_link,
                'linkedin_link' => $user->linkedin_link,
                'youtube_link' => $user->youtube_link,
                'facebook_link' => $user->facebook_link,
            ]
        ]);
    }

    /**
     * Update learner profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'learner') {
            return ApiResponse::sendError('Unauthorized.', 403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'headline' => 'nullable|string|max:255',
            'about' => 'nullable|string|max:1000',
            'twitter_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'youtube_link' => 'nullable|url',
            'facebook_link' => 'nullable|url',
        ]);

        // Update user profile
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->headline = $request->headline;
        $user->about = $request->about;
        $user->twitter_link = $request->twitter_link;
        $user->linkedin_link = $request->linkedin_link;
        $user->youtube_link = $request->youtube_link;
        $user->facebook_link = $request->facebook_link;

        $user->save();

        return ApiResponse::sendResponse(200, 'Profile updated successfully.', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'headline' => $user->headline,
                'about' => $user->about,
                'twitter_link' => $user->twitter_link,
                'linkedin_link' => $user->linkedin_link,
                'youtube_link' => $user->youtube_link,
                'facebook_link' => $user->facebook_link,
            ]
        ]);
    }

    public function closeAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $user = $request->user();

        // Check if already requested
        if ($user->deletion_requested_at) {
            return ApiResponse::sendError('Account closure already requested.', 400);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return ApiResponse::sendError('Password is incorrect.', 400);
        }

        // Mark for deletion
        $user->update([
            'deletion_requested_at' => now()
        ]);

        return ApiResponse::sendResponse(200, 'Account closure requested. You can cancel within 14 days by logging in.');
    }

    /**
     * Get account status
     */
    public function status(Request $request)
    {
        $user = $request->user();
        $status = $user->deletion_requested_at ? 'pending_closure' : 'active';

        $data = ['status' => $status];

        if ($user->deletion_requested_at) {
            $data['closure_date'] = $user->deletion_requested_at->addDays(14)->toISOString();
        }

        return ApiResponse::sendResponse(200, 'Account status retrieved.', $data);
    }
}
