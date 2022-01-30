<?php

namespace App\Http\Requests\Gateways\Razorpay;

use App\Http\Requests\CoreRequest;

class CreateOrderRequest extends CoreRequest
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
            'amount' => 'required',
            'currency' => 'required|in:INR',
        ];

        return $rules;
    }

}
