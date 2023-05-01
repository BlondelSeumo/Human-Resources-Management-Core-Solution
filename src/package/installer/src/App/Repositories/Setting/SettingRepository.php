<?php


namespace Gainhq\Installer\App\Repositories\Setting;

use Gainhq\Installer\App\Models\Setting;
use Gainhq\Installer\App\Repositories\BaseRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SettingRepository extends BaseRepository
{
    protected $settingAble;

    protected $context;

    public function __construct(Setting $setting)
    {
        $this->model = $setting;
    }

    public function getFormattedSettings($context = 'app', $settingable_type = null, $settingable_id = null)
    {
        return $this->formatSettings(
            $this->basicQuery($context, $settingable_type, $settingable_id)
                ->get(['id', 'name', 'value'])
        );
    }

    public function formatSettings(Collection $settings, $decrypt = false)
    {
        return $settings->reduce(function ($final, $setting) use ($decrypt) {
            $final[$setting->name] = $decrypt ? decrypt($setting->value) : $setting->value;
            return $final;
        }, []);
    }

    public function basicQuery($context = null, $settingable_type = null, $settingable_id = null)
    {
        return $this->model::query()->when($context, function (Builder $builder) use ($context) {
            $builder->whereIn('context', is_array($context) ? $context : [$context]);
        })->where('settingable_type', $settingable_type)
            ->where('settingable_id', $settingable_id);
    }

    public function findAppSettingWithName(string $name, string $context = 'app')
    {
        return $this->basicQuery($context, null, null)
            ->where('name', $name)
            ->first();
    }

    public function createSettingInstance(string $name, string $context, $settingable_type = null, $settingable_id = null)
    {
        return $this->basicQuery($context, $settingable_type, $settingable_id)
            ->where('name', $name)
            ->firstOrNew();
    }

    public function getDeliverySettingLists($context = null, $settingable_type = null, $settingable_id = null)
    {
        return $this->formatSettings(
            $this->basicQuery($context, $settingable_type, $settingable_id)
                ->get(['id', 'name', 'value']),
            true
        );

    }

    public function settings($context)
    {
        $context = is_array($context) ? $context : func_get_args();
        return $this->model::query()
            ->whereIn('context', $context)
            ->get();
    }

    public function getDefaultMailKey($key = 'default_mail', $settingable_type = null, $settingable_id = null)
    {
        return $this->model::query()
            ->select(['id', 'name', 'value', 'context'])
            ->where('name', $key)
            ->where('settingable_type', $settingable_type)
            ->where('settingable_id', $settingable_id)
            ->first();
    }

    public function getFromKey($key)
    {
        [$setting_able_id, $setting_able_type] = $this->getSettingAble();

        $context = $this->getContext();

        $setting = $this->basicQuery($context, $setting_able_type, $setting_able_id)
            ->where('name', $key)
            ->select(['name', 'value'])
            ->first();

        $setting = $setting ? $this->formatSettings(collect([ $setting ])) : [$key => ''];

        return isset($setting[$key]) ? $setting[$key] : '';
    }

    public function getFromKeys(array $keys)
    {
        [$setting_able_id, $setting_able_type] = $this->getSettingAble();
        $context = $this->getContext();
        return $this->formatSettings(
            $this->basicQuery($context, $setting_able_type, $setting_able_id)
                ->whereIn('name', $keys)
                ->select(['name', 'value'])
                ->get()
        );
    }

    public function getSettingAble()
    {
        $settingable_type = null;
        $settingable_id = null;

        if ($this->settingAble instanceof Model || $this->settingAble instanceof Authenticatable) {
            $settingable_type = get_class($this->settingAble);
        }

        $settingable_id = optional($this->settingAble)->id ?: null;

        return [
            $settingable_id,
            $settingable_type
        ];
    }

   /* public function setSettingAble($settingAble): SettingRepository
    {
        $this->settingAble = $settingAble;

        return $this;
    }*/

   /* public function getContext(): string
    {
        return $this->context;
    }*/

    /*public function setContext($context): SettingRepository
    {
        $this->context = $context;

        return $this;
    }*/

}
