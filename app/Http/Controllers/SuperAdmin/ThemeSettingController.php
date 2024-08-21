<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Theme\StoreTheme;
use App\Models\ThemeSetting;

class ThemeSettingController extends SuperAdminBaseController
{

    public function update(StoreTheme $request)
    {

        $superAdminThemeSetting = ThemeSetting::ofSuperAdminRole()->first();
        $adminThemeSetting = ThemeSetting::ofAdminRole()->first();
        $customerThemeSetting = ThemeSetting::ofCustomerRole()->first();

        $superAdminThemeSetting->update($request->superadmin);
        $adminThemeSetting->update($request->administrator);
        $customerThemeSetting->update($request->customer);

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
