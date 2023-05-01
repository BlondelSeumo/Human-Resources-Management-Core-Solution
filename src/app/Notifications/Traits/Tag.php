<?php


namespace App\Notifications\Traits;


trait Tag
{
    public function commonForSubject()
    {
        return [
            '{tenant_name}' => settings('tenant_name', 'app_name'),
            '{app_name}' => settings('tenant_name', 'app_name'),
            '{company_name}' => settings('tenant_name', 'app_name'),
            '{action_by}' => optional(auth()->user())->full_name
        ];
    }

    public function commonTagForSystem()
    {
        return $this->commonForSubject();
    }

    public function systemTemplateModifier($vars)
    {
        return array_map(function ($var) {
            return '<b>'.$var.'</b>';
        },$vars);
    }
}
