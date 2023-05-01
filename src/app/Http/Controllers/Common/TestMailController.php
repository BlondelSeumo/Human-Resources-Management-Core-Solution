<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Mail\Common\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    public function send(Request $request)
    {
        validator($request->all(),[
            'email' => ['required','email'],
            'subject' => ['required'],
            'message' => ['required']
        ])->validate();

        try {
            Mail::to($request->email)
                ->send(new TestMail($request->subject, $request->message));
            return response(['status' => true, 'message' => __t('email_sent_successfully')]);
        }catch (\Exception $exception){
            throw new GeneralException(__t('email_setup_is_not_correct'));
        }

    }
}
