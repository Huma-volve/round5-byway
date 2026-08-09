<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Course extends Model
{
    use HasFactory,Searchable;

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'video_url',
        'status',
        'price',
        'rating',
        'image',
        'category_id',
        'user_id',
    ];

    protected $casts = [
'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'created_at' => 'datetime',

        'price' => 'decimal:2',

        'updated_at' => 'datetime',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }



    //دالة لحساب متوسط التقييمات//

    public function updateRating()
    {
        $avg = $this->reviews()->avg('rating');
        $this->rating = $avg ?? 0;
        $this->save();
    }





    // ✅ Accessor يضيف progress ثابت (مثلاً 20%)
    public function getProgressAttribute()
    {
        return "20%"; // ممكن بعدين نحسبها على حسب تقدم الطالب
    }
    public function toSearchableArray()
    {
        $array = [
            'id'             => $this->id,
            'title'          => $this->title,
            'instructor_name' => $this->instructor?->name,
            'category_name'  => $this->category?->name,
            'status'         => $this->status,
        ];

        return $array;
    }

}
