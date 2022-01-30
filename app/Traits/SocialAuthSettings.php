<?php

namespace App\Traits;

use App\SocialAuthSetting;
use Illuminate\Support\Facades\Config;

trait SocialAuthSettings
{

    public function setSocailAuthConfigs()
    {
        $settings = SocialAuthSetting::first();

        Config::set('services.facebook.client_id', ($settings->facebook_client_id) ? $settings->facebook_client_id : env('FACEBOOK_CLIENT_ID'));
        Config::set('services.facebook.client_secret', ($settings->facebook_secret_id) ? $settings->facebook_secret_id : env('FACEBOOK_CLIENT_SECRET'));
        Config::set('services.facebook.redirect', route('social.login-callback', 'facebook'));

        Config::set('services.google.client_id', ($settings->google_client_id) ? $settings->google_client_id : env('GOOGLE_CLIENT_ID'));
        Config::set('services.google.client_secret', ($settings->google_secret_id) ? $settings->google_secret_id : env('GOOGLE_CLIENT_SECRET'));
        Config::set('services.google.redirect', route('social.login-callback', 'google'));
    }

}
