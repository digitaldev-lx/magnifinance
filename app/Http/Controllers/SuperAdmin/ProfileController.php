<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Services\ImagesManager;
use App\User;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Setting\ProfileSetting;

class ProfileController extends SuperAdminBaseController
{

    private $image;

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.profile'));
        $this->image = new ImagesManager();
    }

    public function index()
    {
        $user = $this->user;
        return view('superadmin.profile.index', compact('user'));
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
            $this->image->deleteImage($user->image);
            $user->image = $this->image->storeImage($request, 'avatar');
        }

        $user->save();

        return Reply::redirect(route('superadmin.profile.index'), __('messages.updatedSuccessfully'));
    }

}
