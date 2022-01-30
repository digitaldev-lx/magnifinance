<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\SocialAuthSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\SocialAuth\UpdateRequest;

class SocialAuthSettingController extends Controller
{

    public function update(UpdateRequest $request)
    {
        $socialAuth = SocialAuthSetting::first();

        $socialAuth->facebook_client_id = $request->facebook_client_id;
        $socialAuth->facebook_secret_id = $request->facebook_secret_id;
        ($request->facebook_status) ? $socialAuth->facebook_status = 'active' : $socialAuth->facebook_status = 'inactive';

        $socialAuth->google_client_id = $request->google_client_id;
        $socialAuth->google_secret_id = $request->google_secret_id;
        ($request->google_status) ? $socialAuth->google_status = 'active' : $socialAuth->google_status = 'inactive';

        $socialAuth->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
