<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\User;
use App\Http\Requests\Setting\ProfileSetting;

class ProfileController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.profile'));
    }

    public function index()
    {
        $user = $this->user;
        return view('admin.profile.index', compact('user'));
    }

    public function store(ProfileSetting $request)
    {
        $user = User::find($this->user->id);
        $user->name = $request->name;
        $user->email = $request->email;

        if($request->password != ''){
            $user->password = $request->password;
        }

        if ($request->has('mobile')) {
            if ($user->mobile !== $request->mobile || $user->calling_code !== $request->calling_code) {
                $user->mobile_verified = 0;
            }

            $user->mobile = $request->mobile;
            $user->calling_code = $request->calling_code;
        }

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image, 'avatar');
        }

        $user->save();

        return Reply::redirect(route('admin.profile.index'), __('messages.updatedSuccessfully'));
    }

}
