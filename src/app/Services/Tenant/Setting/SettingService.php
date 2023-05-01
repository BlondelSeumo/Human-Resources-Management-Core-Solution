<?php


namespace App\Services\Tenant\Setting;

use App\Helpers\Traits\TenantAble;
use App\Models\Core\Setting\Setting;
use App\Services\Core\BaseService;
use App\Helpers\Core\Traits\FileHandler;
use App\Repositories\Core\Setting\SettingRepository;

class SettingService extends BaseService
{
    use FileHandler, TenantAble;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function update()
    {
        $settings = request()->except('allowed_resource', 'tenant_id', 'tenant_short_name');

        collect(array_keys($settings))->each(function ($key) use ($settings) {

            [$setting_able_id, $setting_able_type] = $this->tenantAble();

            $setting = resolve(SettingRepository::class)
                ->createSettingInstance(
                    $key,
                    'tenant',
                    $setting_able_type,
                    $setting_able_id
                );

            if (request()->file($key)) {
                $this->deleteImage(optional($setting)->value);
                $settings[$key] = $this->uploadImage(request()->file($key), config('file.' . $key . '.folder'), config('file.' . $key . '.height'));
            }

            $this->setModel($setting);

            return parent::save([
                'name' => $key,
                'value' => $settings[$key],
                'context' => 'tenant',
                'settingable_id' => $setting_able_id,
                'settingable_type' => $setting_able_type,
            ]);
        });
    }


    public function getFormattedTenantSettings($context = 'tenant')
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        return resolve(SettingRepository::class)
            ->getFormattedSettings($context, $setting_able_type, $setting_able_id);
    }
}
