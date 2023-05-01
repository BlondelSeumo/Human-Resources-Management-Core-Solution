<?php


namespace App\Models\Core\Setting\Traits;


use Illuminate\Support\Facades\DB;

trait NotificationSettingMethodTrait
{
    public function users()
    {
        return collect($this->audiences)->reduce(function ($audiences, $audience) {
            $users = [];
            if ($audience->audience_type == 'roles') {
                $users = DB::table('role_user')
                    ->whereIn('role_id', $audience->audiences)
                    ->pluck('user_id')
                    ->toArray();
            }elseif ($audience->audience_type = 'users') {
                $users = $audience->audiences;
            }

            return array_merge($audiences, $users);
        }, []);
    }

}
