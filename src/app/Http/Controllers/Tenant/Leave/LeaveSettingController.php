<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Helpers\Traits\TenantAble;
use App\Http\Controllers\Controller;
use App\Repositories\Core\BaseRepository;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\SettingService;
use Illuminate\Http\Request;

class LeaveSettingController extends Controller
{
    use TenantAble;

    protected BaseRepository $repository;

    public function __construct(SettingService $service, SettingRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        return $this->repository->getFormattedSettings(
            'leave', $setting_able_type, $setting_able_id
        );
    }

    public function update(Request $request)
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $attributes = array_merge($request->only('start_month', 'approval_level'),
            [
                'allow_bypass' => (int)$request->get('allow_bypass', 0),
                'statuses_for_paid_leave' => is_array($request->get('statuses_for_paid_leave'))
                    ? json_encode($request->get('statuses_for_paid_leave', []))
                    : $request->get('statuses_for_paid_leave'),
                'statuses_for_unpaid_leave' => is_array($request->get('statuses_for_unpaid_leave'))
                    ? json_encode($request->get('statuses_for_unpaid_leave', []))
                    : $request->get('statuses_for_unpaid_leave'),
                'users' => is_array($request->get('users'))
                    ? json_encode($request->get('users', []))
                    : $request->get('users'),
                'roles' => is_array($request->get('roles'))
                    ? json_encode($request->get('roles', []))
                    : $request->get('roles')
            ]);

        $this->service->saveSettings(
            $attributes,
            'leave',
            $setting_able_type,
            $setting_able_id
        );

        return updated_responses('leave_settings');
    }


}
