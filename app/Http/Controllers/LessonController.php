<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Store a newly created lesson in storage.
     */
    public function store(Request $request, Course $course)
    {
        // 1. تحقق إن الكورس تبع الـ instructor الحالي
        if ($course->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Validate البيانات
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4,mov,avi|max:512000' // 500MB
        ]);

        // 3. رفع الفيديو وتخزينه
        $videoPath = $request->file('video')->store('lessons/videos', 'public');

        // 4. إنشاء lesson وربطه بالكورس
        $lesson = $course->lessons()->create([
            'title' => $validated['title'],
            'video_url' => $videoPath, // نخزن المسار في الـ DB
        ]);

        return response()->json([
            'message' => 'Lesson created successfully',
            'lesson' => $lesson
        ], 201);
    }

    /**
     * Display a specific lesson.
     */
    public function show(Course $course, Lesson $lesson)
    {
        // تحقق إن الليسن تبع الكورس
        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'Lesson not found'], 404);
        }

        return response()->json($lesson);
    }

    /**
     * Update a specific lesson.
     */
    public function update(Request $request, Course $course, Lesson $lesson)
    {
        // تحقق إن صاحب الكورس هو المستخدم الحالي
        if ($course->instructor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'Lesson not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:512000'
        ]);

        // لو فيه فيديو جديد
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('lessons/videos', 'public');
            $validated['video_url'] = $videoPath;
        }

        $lesson->update($validated);

        return response()->json([
            'message' => 'Lesson updated successfully',
            'lesson' => $lesson
        ]);
    }

    /**
     * Remove a specific lesson.
     */
    public function destroy(Course $course, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'Lesson not found'], 404);
        }

        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted successfully']);
    }
}