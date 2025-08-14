<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;
use App\Helpers\ApiResponse;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:learner,teacher',
        ]);

        // Generate 6-digit verification code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'verification_code' => $code,
            'is_verified' => false,
        ]);

        // Send verification email
        $this->sendVerificationEmail($user);

        return ApiResponse::sendResponse(201, 'Registration successful. Check your email for the verification code.', [
            'user_id' => $user->id,
            'name'=>$user->name,
            'role' => $user->role,
            'email' => $user->email,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
    }

    /**
     * Send verification code to email.
     */
    protected function sendVerificationEmail($user)
    {
        Mail::raw("Your verification code is: {$user->verification_code}", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Account');
        });
    }

    /**
     * Verify email with code.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->verification_code !== $request->code) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        $user->is_verified = true;
        $user->verification_code = null; // clear code
        $user->save();

        // Optionally create API token here, or let user login
        return response()->json([
            'message' => 'Email verified successfully. You can now log in.',
        ]);
    }

    /**
     * Redirect to Google OAuth.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle Google callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Google authentication failed.'], 401);
        }

        $user = User::updateOrCreate([
            'provider' => 'google',
            'provider_id' => $googleUser->getId(),
        ], [
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
            'is_verified' => true,
            'role' => 'learner', // default; you can upgrade later
            'password' => $password ??= Hash::make(Str::random(24)), // required but not used
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in with Google.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'is_verified' => $user->is_verified,
            ]
        ]);
    }
}
