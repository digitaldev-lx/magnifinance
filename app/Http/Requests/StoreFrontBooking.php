<?php

namespace App\Http\Requests;

use App\GoogleCaptchaSetting;
use Illuminate\Foundation\Http\FormRequest;

class StoreFrontBooking extends CoreRequest
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
        $rules = [];
        $google_captcha = GoogleCaptchaSetting::first();

        if(auth()->guest()){
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|numeric',
                'calling_code' => 'required_with:phone',
            ];
        }

        if($google_captcha->status == 'active' && $google_captcha->customer_page == 'active')
        {
            $rules['recaptcha'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'email.unique' => __('front.emailRegistered')
        ];
    }

}
