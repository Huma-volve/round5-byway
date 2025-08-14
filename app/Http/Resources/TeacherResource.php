<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'bio'=>$this->bio,
            'total_earnings'=>$this->total_earnings,
            'twitter_link'=>$this->twitter_link,
            'linkdin_link'=>$this->linkdin_link,
            'youtube_link'=>$this->youtube_link,
            'facebook_link'=>$this->facebook_link,
            'name'=>$this->name,
            'role'=>$this->role,
            'image' => asset('storage/' . $this->image),


        ];
    }
}
