<?php

namespace App\Http\Controllers;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Helpers\ApiResponse;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function store (request $request){
        $validatecourses= $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'required|string',
             'video'=>'required|file|mimes:mp4,mov,avi|max:204800', // 200MB
            'status'=> 'required|in:published,unpublished',
            'price'=> 'required|numeric|min:0',
            'category_id'=>'required|exists:categories,id']);



                   //رفع الفيديو Cloudinary//
        $uploadedFile = Cloudinary::uploadFile(
            $request->file('video')->getRealPath(),
            ['resource_type' => 'video']
        );
        $videoUrl = $uploadedFile->getSecurePath();


        $course=Course::create([
'user_id'=>auth()->id(),
'title'=>$validatecourses['title'],
'description'=>$validatecourses['description'],
'video_url'=> $videoUrl ,
'status'=>$validatecourses['status'],
'price'=>$validatecourses['price'],
'category_id'=>$validatecourses['category_id']

        ]);
        return ApiResponse::sendResponse(200, 'Course created successfully', $course);

    }
}
