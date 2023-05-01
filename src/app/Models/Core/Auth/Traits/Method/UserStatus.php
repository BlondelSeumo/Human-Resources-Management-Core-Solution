<?php


namespace App\Models\Core\Auth\Traits\Method;


use App\Exceptions\GeneralException;
use App\Models\Core\Status;
use App\Repositories\Core\Status\StatusRepository;

trait UserStatus
{
    public function isActive()
    {
        return optional($this->status)->name == 'status_active';
    }

    public function isInvited()
    {
        return optional($this->status)->name == 'status_invited';
    }

    public function isInactive()
    {
        return optional($this->status)->name == 'status_inactive';
    }

    public function markAs($status)
    {
        throw_if(
            is_array($status),
            new GeneralException('Status can\'t be an array')
        );

        if ($status instanceof Status) {
            $status = $status->id;
        }elseif (is_string($status)) {
            $methodName = 'user'.ucfirst($status);
            $status = resolve(StatusRepository::class)->$methodName();
        }

        $this->fill([
            'status_id' => $status
        ]);

        $this->save();


    }
}