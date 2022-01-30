<?php

namespace App\Http\Requests\ApplyCoupon;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class ApplyRequest extends CoreRequest
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
        return [
            'coupon'      => 'required',
        ];
    }

    public function messages()
    {
        return [
            'coupon.required' => __('app.coupon').' '.__('app.code').' '.__('errors.fieldRequired'),
        ];
    }

}
