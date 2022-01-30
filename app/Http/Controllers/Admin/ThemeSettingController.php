<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Theme\AdminStoreTheme;
use App\ThemeSetting;

class ThemeSettingController extends AdminBaseController
{

    public function update(AdminStoreTheme $request, $id)
    {
        $theme = ThemeSetting::find($id);

        $theme->primary_color = $request->primary_color;
        $theme->secondary_color = $request->secondary_color;
        $theme->sidebar_bg_color = $request->sidebar_bg_color;
        $theme->sidebar_text_color = $request->sidebar_text_color;
        $theme->topbar_text_color = $request->topbar_text_color;
        $theme->custom_css = $request->admin_custom_css;

        $theme->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
