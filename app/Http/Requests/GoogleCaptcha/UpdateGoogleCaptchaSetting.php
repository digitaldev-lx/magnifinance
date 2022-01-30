<?php

namespace App\Http\Requests\GoogleCaptcha;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoogleCaptchaSetting extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (!$this->has('google_captcha_status')) {
            $this->request->add(['google_captcha_status' => 'deactive']);
        }

        return [

            'version' => 'required',

            'google_captcha2_site_key' => 'required_if:version,2',
            'google_captcha2_secret' => 'required_if:version,2',

            'google_captcha3_site_key' => 'required_if:version,3',
            'google_captcha3_secret' => 'required_if:version,3',

        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'google_captcha2_site_key.required_if' => 'Site key is required',
            'google_captcha2_secret.required_if' => 'Secret key is required',
            'google_captcha3_site_key.required_if' => 'Site key is required',
            'google_captcha3_secret.required_if' => 'Secret key is required',
        ];
    }

}
