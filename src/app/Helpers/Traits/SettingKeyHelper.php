<?php


namespace App\Helpers\Traits;


use App\Repositories\Core\Setting\SettingRepository;
use Closure;

trait SettingKeyHelper
{
    public function getSettingFromKey($context, $settingAble = null): Closure
    {
        return fn(string $key) => $this->buildSettingAble($context, $settingAble)->getFromKey($key);
    }

    public function getSettingFromKeys($context, $settingAble = null): Closure
    {
        return function ($keys) use ($context, $settingAble) {
            $keys = is_array($keys) ? $keys : func_get_args();

            return $this->buildSettingAble($context, $settingAble)
                ->getFromKeys($keys);
        };
    }

    private function buildSettingAble($context, $settingAble = null)
    {
        $settingAble = $settingAble ? $settingAble : tenant();

        return resolve(SettingRepository::class)
            ->setSettingAble($settingAble)
            ->setContext($context);
    }
}