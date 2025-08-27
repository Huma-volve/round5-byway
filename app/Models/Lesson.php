<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'video_duration',
        'materials',
        'order',
        'course_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'materials' => 'array',
        'video_duration' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // تنسيق مدة الفيديو
    public function getFormattedDurationAttribute()
    {
        if (!$this->video_duration) {
            return 'غير محدد';
        }

        $hours = floor($this->video_duration / 3600);
        $minutes = floor(($this->video_duration % 3600) / 60);
        $seconds = $this->video_duration % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    // الحصول على المواد كأراي
    public function getMaterialsArrayAttribute()
    {
        return $this->materials ?? [];
    }

    public function lessonCompletions()
    {
        return $this->hasMany(LessonCompletion::class);
    }
}
