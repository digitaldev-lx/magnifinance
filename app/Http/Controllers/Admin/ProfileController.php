<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Setting\ProfileSetting;
use App\Models\Country;
use App\Services\ImagesManager;
use App\User;

class ProfileController extends AdminBaseController
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
        $countries = Country::all();
        return view('admin.profile.index', compact(['user', 'countries']));
    }

    public function store(ProfileSetting $request)
    {
        $user = User::find($this->user->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->post_code = $request->post_code;
        $user->country_id = $request->country_id;
        $user->vat_number = $request->vat_number;
        $user->mobile = $request->mobile;
        $user->calling_code = $request->calling_code;

        if($request->password != ''){
            $user->password = $request->password;
        }

        /*if ($request->has('mobile')) {
            if ($user->mobile !== $request->mobile || $user->calling_code !== $request->calling_code) {
                $user->mobile_verified = 0;
            }

            $user->mobile = $request->mobile;
            $user->calling_code = $request->calling_code;
        }*/

        if ($request->hasFile('image')) {
            $this->image->deleteImage($user->image);
            $user->image = $this->image->storeImage($request, 'avatar');
        }

        $user->save();

        return Reply::redirect(route('admin.profile.index'), __('messages.updatedSuccessfully'));
    }

}
