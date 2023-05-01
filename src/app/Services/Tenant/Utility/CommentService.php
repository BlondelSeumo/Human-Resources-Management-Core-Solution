<?php


namespace App\Services\Tenant\Utility;


use App\Exceptions\GeneralException;
use App\Models\Tenant\Utility\Comment;
use App\Services\Tenant\TenantService;

class CommentService extends TenantService
{
    public function __construct(Comment $comment)
    {
        $this->model = $comment;
    }

    public function validate()
    {
        validator($this->getAttributes('comment'), ['comment' => 'required|min:2'])
            ->validate();

        return $this;
    }

    public function validateOwner()
    {
        throw_if(
            $this->model->user_id != auth()->id(),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function duplicate()
    {
        $this->model->replicate(['user_id', 'comment', 'parent_id'])
            ->fill(
                array_merge([
                    'user_id' => auth()->id(),
                    'parent_id' => $this->model->id
                ], $this->getAttributes('comment'))
            )->save();

        return $this;
    }

}