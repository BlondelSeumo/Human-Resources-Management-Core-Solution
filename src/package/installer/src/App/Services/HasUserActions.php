<?php


namespace Gainhq\Installer\App\Services;


use App\Hooks\User\BeforeRoleAttachedToUser;
use App\Hooks\User\BeforeRoleDetachedFromUser;
use App\Hooks\User\BeforeUserDeleted;
use App\Hooks\User\BeforeUserUpdated;
use App\Notifications\Core\User\UserNotification;

trait HasUserActions
{
    public function beforeUpdate()
    {
        BeforeUserUpdated::new(true)
            ->setModel($this->model)
            ->handle();

        return $this;
    }

    public function beforeDelete()
    {
        BeforeUserDeleted::new(true)
            ->setModel($this->model)
            ->handle();

        return $this;
    }

    public function beforeAttachingRole()
    {
        BeforeRoleAttachedToUser::new()
            ->setModel($this->model)
            ->handle();

        return $this;
    }

    public function beforeDetachingRole()
    {
        BeforeRoleDetachedFromUser::new()
            ->setModel($this->model)
            ->handle();

        return $this;
    }

    public function notify(string $event, $user = null)
    {
        notify()
            ->on($event)
            ->with($user ?: $this->model)
            ->send(UserNotification::class);

        return $this;
    }
}