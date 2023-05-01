<?php


namespace App\Hooks\User;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;

class BeforeLogin extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        throw_if(optional($this->model->employmentStatus->first())->alias == 'terminated',
            new GeneralException(__t('you_have_been_terminated'))
        );
        return $this->model;
    }
}
