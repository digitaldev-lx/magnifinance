<?php

namespace App\Rules;

use App\GoogleCaptchaSetting;
use Illuminate\Contracts\Validation\Rule;
use ReCaptcha\ReCaptcha;

class Captcha implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    // @codingStandardsIgnoreLine
    public function passes($attribute, $value)
    {
        $captcha = new ReCaptcha(GoogleCaptchaSetting::first()->v2_secret_key);
        $response = $captcha->verify($value, $_SERVER['REMOTE_ADDR']);

        return $response->isSuccess();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return (__('messages.completeReCaptcha'));
    }

}
