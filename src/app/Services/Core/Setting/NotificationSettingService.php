<?php


namespace App\Services\Core\Setting;

use App\Models\Core\Setting\NotificationSetting;
use App\Services\Core\BaseService;

class NotificationSettingService extends BaseService
{
    public function __construct(NotificationSetting $setting)
    {
        $this->model = $setting;
    }


    public function update(NotificationSetting $setting)
    {
        $this->setModel($setting);

        parent::save(array_merge(
            request()->all(),
            ['updated_by' => auth()->id()]
        ));

        return $this->model;
    }

    public function attachAudiences(array $audiences, $attach = true)
    {
        if (!$attach) {
            $this->model
                ->audiences()
                ->delete();
        }

        return $this->model
            ->audiences()
            ->createMany($audiences);

    }

    public function syncAudiences(array $audiences)
    {
        return $this->attachAudiences($audiences, false);
    }
}
