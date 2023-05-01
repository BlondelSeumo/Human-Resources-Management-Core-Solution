<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

use Gainhq\Installer\App\Exceptions\GeneralException;

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

        $this->fill([
            'status_id' => $status
        ]);

        $this->save();
    }
}