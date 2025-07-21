<?php

namespace Modules\Review\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Comment\Http\Resources\CommentResource;
use Modules\User\Resources\UserResource;

class ReviewOldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'postedAt' => $this->created_at,
            'comment' => $this->comment,
            'isPurchased' => $this->is_purchased,
            'rating' => $this->rating,
            'avatarUrl' => $this->user->avatar_url,
            'helpful' => $this->helpful,
            'attachments' => $this->attachments->pluck('url'),
            'user' => new UserResource($this->user),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
