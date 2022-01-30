<?php

namespace App\Http\Controllers\SuperAdmin;

use App\SmtpSetting;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Notifications\TestEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Mail\UpdateSmtpSetting;
use App\Http\Controllers\SuperAdminBaseController;

class SmtpSettingController extends SuperAdminBaseController
{

    public function update(UpdateSmtpSetting $request)
    {


        $smtp = SmtpSetting::first();

        $data = $request->all();

        if ($request->mail_encryption == 'null' || $request->mail_encryption == 'none') {
            $data['mail_encryption'] = null;
        }

        $smtp->update($data);
        $response = $smtp->verifySmtp();

        if ($smtp->mail_driver == 'mail') {
            return Reply::success(__('messages.updatedSuccessfully'));
        }

        if ($response['success']) {
            return Reply::success($response['message']);
        }

        // GMAIL SMTP ERROR
        $message = __('messages.smtpError').'<br><br> ';

        if ($smtp->mail_host == 'smtp.gmail.com')
        {
            $secureUrl = 'https://myaccount.google.com/lesssecureapps';
            $message .= __('messages.smtpSecureEnabled');
            $message .= '<a  class="font-13" target="_blank" href="' . $secureUrl . '">' . $secureUrl . '</a>';
            $message .= '<hr>' . $response['message'];
            return Reply::error($message);
        }

        return Reply::error($message . '<hr>' . $response['message']);

    }

    public function sendTestEmailModal()
    {
        return view('superadmin.email.create');
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $smtp = SmtpSetting::first();
        $response = $smtp->verifySmtp();

        if ($response['success']) {
            Notification::route('mail', \request()->test_email)->notify(new TestEmail());
            return Reply::success(__('app.testEmailSuccess'));
        }
        
        return Reply::error($response['message']);
    }

}
