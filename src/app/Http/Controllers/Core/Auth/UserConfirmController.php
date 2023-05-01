<?php

namespace App\Http\Controllers\Core\Auth;

use App\Exceptions\GeneralException;
use App\Hooks\User\AfterUserConfirmed;
use App\Http\Controllers\Controller;
use App\Jobs\User\UserInvited;
use App\Models\Core\Status;
use App\Notifications\Core\User\UserInvitationNotification;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Core\Auth\UserService;
use App\Http\Requests\Core\Auth\User\UserConfirmRequest as Request;

class UserConfirmController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $token = str_replace('}', '', \request()->invitation_token);

        $user = $this->service->with('status')->where('invitation_token', $token)->first();

        if ($user && optional($user->status)->name == 'status_invited') {
            return view('frontend.user.invitation_confirm', ['user' => $user]);
        }

        throw new GeneralException(trans('default.invalid_token'));

    }

    public function confirm(Request $request)
    {
        $status = resolve(StatusRepository::class)->userActive();

        $user = $this->service
            ->with('status')
            ->where('invitation_token', $request->get('invitation_token'))
            ->firstOrFail();

        throw_if(
            optional($user->status)->name != 'status_invited',
            new GeneralException(__t('action_not_allowed'))
        );

        $attributes = array_merge($request->only('first_name', 'password', 'last_name'), ['status_id' => $status]);
        $user->fill($attributes)
            ->save();

        AfterUserConfirmed::new()
            ->setModel($user)
            ->handle();

        notify()
            ->on('user_joined')
            ->with($user)
            ->send(UserInvitationNotification::class);

        log_to_database(trans('default.user_confirm_joining'), [
            'old' => [],
            'attributes' => $user
        ]);

        return response()->json([
            'status' => true,
            'message' => trans('default.user_account_confirmed')
        ]);
    }
}
