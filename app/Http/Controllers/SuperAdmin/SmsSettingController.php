<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Sms\UpdateSmsSetting;
use App\SmsSetting;

class SmsSettingController extends SuperAdminBaseController
{

    // @codingStandardsIgnoreLine
    public function update(UpdateSmsSetting $request, $id)
    {
        if ($request->nexmo_status == 'active' && $request->msg91_status == 'active') {
            return Reply::error(__('messages.smsGateway.onlyOne'));
        }

        $sms_setting = SmsSetting::first();

        $sms_setting->nexmo_key = $request->nexmo_key;
        $sms_setting->nexmo_secret = $request->nexmo_secret;
        $sms_setting->nexmo_from = $request->nexmo_from;
        $sms_setting->nexmo_status = $request->nexmo_status;

        $sms_setting->msg91_key = $request->msg91_key;
        $sms_setting->msg91_from = $request->msg91_from;
        $sms_setting->msg91_status = $request->msg91_status;

        $sms_setting->save();

        return Reply::redirect(route('superadmin.settings.index'), __('messages.updatedSuccessfully'));
    }

}
