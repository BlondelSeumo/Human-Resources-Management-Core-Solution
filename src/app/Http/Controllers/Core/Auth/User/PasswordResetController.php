<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Auth\User\traits\PasswordResetTrait;
use App\Http\Requests\Core\Auth\User\PasswordResetRequest as Request;
use App\Http\Requests\Core\Auth\User\ResetPasswordRequest;
use App\Mail\Core\User\PasswordResetMail;
use App\Models\Core\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request as BaseRequest;

class PasswordResetController extends Controller
{
    use PasswordResetTrait;

    public function index()
    {
        return view('frontend.user.password_reset');
    }

    public function store(Request $request)
    {
        /** @var User $user*/
        ['user' => $user] = $this->tokenAndUser($request->get('email'));

        if (!$user)
            return response()->json(['status' => false, 'message' => trans('default.no_user_found_on_that_email')], 404);

        $token = base64_encode(microtime(true));

        DB::table('password_resets')->insert([
            'email' => $request->get('email'),
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($user)
            ->send(
                (new PasswordResetMail($user, $token))
                    ->onQueue('high')
                    ->delay(5)
            );

        return response()->json(['status' => true, 'message' => trans('default.password_reset_mail_has_been_sent_successfully')]);
    }

    public function show(BaseRequest $request)
    {
        $request->validate([
            'token' => 'required|min:10',
            'email' => 'required|email'
        ]);

        ['user' => $user, 'token' => $token] = $this->tokenAndUser($request->get('email'), $request->get('token'));

        throw_if(!($token && $user), new GeneralException(trans('default.invalid_token')));

        if (Carbon::parse($token->created_at)->diffInMinutes(Carbon::now()) >= 20) {
            throw_if(!($token && $user), new GeneralException(trans('default.invalid_token')));
        }

        return view('frontend.user.reset_password', ['user' => $user, 'token' => $request->get('token')]);

    }

    public function update(ResetPasswordRequest $request)
    {
        /** @var User $user*/
        ['user' => $user, 'token' => $token] = $this->tokenAndUser($request->get('email'), $request->get('token'));

        throw_if(!($token && $user), new GeneralException(trans('default.invalid_token')));

        if (Carbon::parse($token->created_at)->diffInMinutes(Carbon::now()) >= 20) {
            throw_if(!($token && $user), new GeneralException(trans('default.invalid_token')));
        }

        $user->update([
           'password' => $request->get('password')
        ]);

        auth()->login($user);

        return response()->json([
            'status' => true,
            'message' => trans('default.password_has_been_reset_successfully'),
            'redirect' => route(home_route()['route_name'], ['params' => home_route()['route_params']])
        ]);

    }
}
