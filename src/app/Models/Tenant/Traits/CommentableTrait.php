<?php


namespace App\Models\Tenant\Traits;


use App\Models\Tenant\Utility\Comment;

trait CommentableTrait
{
    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }
}