<?php

namespace App\Http\Requests\Company;

use App\GoogleCaptchaSetting;
use App\Http\Requests\CoreRequest;
use App\Rules\Captcha;

class RegisterCompany extends CoreRequest
{

    public function rules()
    {
        $google_captcha = GoogleCaptchaSetting::first();

        $rules = [
            'business_name' => 'required',
            'email' => 'required|email|unique:companies,company_email|unique:users,email',
            'contact' => 'required',
            'address' => 'required',
            'city' => 'required',
            'country_id' => 'required',
            'name' => 'required',
            'password' => 'required|min:6'
            ];

        if($google_captcha->status == 'active' && $google_captcha->vendor_page == 'active')
        {
            $rules['recaptcha'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'business_name.required' => 'A business_name is required',
            'contact.required' => 'A contact is required',
            'address.required' => 'A address is required',
            'city.required' => 'A city is required',
            'country_id.required' => 'A country is required',
            'name.required' => 'A name is required',
            'email.required' => 'A email is required',
            'email.unique' => 'Email must be unique',
            'body.required'  => 'A message is required',
        ];
    }

}
