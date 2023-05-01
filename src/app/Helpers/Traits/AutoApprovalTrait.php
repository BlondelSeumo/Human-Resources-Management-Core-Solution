<?php


namespace App\Helpers\Traits;


trait AutoApprovalTrait
{
    public function autoSettingsApproval($settings, $permissions = '', $type = 'attendance'): bool
    {
        if (($permissions && auth()->user()->can($permissions))){
            return true;
        }

        if(!(array)$settings){
            return false;
        }

        if (($type == 'leave' && $settings->approval_level == 'multi')){
            return false;
        }

        if (!boolval(optional($settings)->auto_approval) && $type == 'attendance'){
            return false;
        }

        if (in_array(auth()->id(), optional($settings)->users ? json_decode($settings->users) : []) ||
                    auth()->user()->hasRole(optional($settings)->roles ? json_decode($settings->roles) : [])
        ){
            return true;
        }

        return false;
    }
}