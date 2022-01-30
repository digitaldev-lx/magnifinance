<?php

namespace App\Http\Controllers\SuperAdmin;

use App\GoogleCaptchaSetting;
use App\Helper\Reply;
use App\Http\Requests\GoogleCaptcha\UpdateGoogleCaptchaSetting;
use App\Http\Controllers\SuperAdminBaseController;

class GoogleCaptchaSettingController extends SuperAdminBaseController
{

    public function index()
    {
        $this->key = request()->key;
        return view('superadmin.front-settings.verify-recaptcha-v3', $this->data);
    }

    // @codingStandardsIgnoreLine
    public function update(UpdateGoogleCaptchaSetting $request, $id)
    {
        $google_capcha_setting = GoogleCaptchaSetting::first();

        if($request->version == 'v3') {
            $google_capcha_setting->v3_site_key = $request->google_captcha3_site_key;
            $google_capcha_setting->v3_secret_key = $request->google_captcha3_secret;
            $google_capcha_setting->v3_status = 'active';
            $google_capcha_setting->v2_status = 'deactive';
        }
        else {
            $google_capcha_setting->v2_site_key = $request->google_captcha2_site_key;
            $google_capcha_setting->v2_secret_key = $request->google_captcha2_secret;
            $google_capcha_setting->v2_status = 'active';
            $google_capcha_setting->v3_status = 'deactive';
        }

        if($request->google_captcha_status == 'deactive') {
            $google_capcha_setting->v2_status = 'deactive';
            $google_capcha_setting->v3_status = 'deactive';
            $google_capcha_setting->status = 'deactive';
        }
        else {
            $google_capcha_setting->status = 'active';
        }

        $google_capcha_setting->login_page = $request->login_page ? $request->login_page : 'inactive';
        $google_capcha_setting->customer_page = $request->customer_registration_page ? $request->customer_registration_page : 'inactive';
        $google_capcha_setting->vendor_page = $request->vendor_registration_page ? $request->vendor_registration_page : 'inactive';

        $google_capcha_setting->save();

        return Reply::redirect(route('superadmin.settings.index'), __('messages.updatedSuccessfully'));
    }

}
