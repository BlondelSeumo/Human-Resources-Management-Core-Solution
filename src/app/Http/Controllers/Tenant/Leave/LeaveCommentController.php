<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Utility\Comment;
use App\Services\Tenant\Utility\CommentService;
use Illuminate\Http\Request;

class LeaveCommentController extends Controller
{
    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function update(Comment $comment, Request $request)
    {
        $this->service
            ->setModel($comment)
            ->setAttributes(['comment' => $request->get('description')])
            ->validate()
            ->validateOwner()
            ->duplicate();

        return updated_responses('leave_note');
    }
}
