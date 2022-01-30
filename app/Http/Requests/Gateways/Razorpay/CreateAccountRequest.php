<?php

namespace App\Http\Requests\Gateways\Razorpay;

use App\Http\Requests\CoreRequest;

class CreateAccountRequest extends CoreRequest
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
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'business_name' => 'required',
            'business_type' => 'required',
            'beneficiary_name' => 'required',
            'ifsc_code' => 'required|size:11',
            'account_number' => 'required|confirmed',
            'account_number_confirmation' => 'required',
            'tnc_accepted' => 'required',
        ];

        return $rules;
    }

}
