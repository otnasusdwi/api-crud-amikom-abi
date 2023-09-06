<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
          'id' => $this->id,  
          'image' => $this->image,  
          'title' => $this->title,  
          'content' => $this->content,  
          'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),  
        ];
    }
}
