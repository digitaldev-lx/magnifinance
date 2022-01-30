<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends CoreRequest
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
            'code' => 'required|regex:/(^[A-Za-z0-9]+$)+/|unique:coupons,code,'.$this->route('coupon'),
            'start_time' => 'required',
            'title'      => 'required'
        ];

        if($this->get('amount') == null && $this->get('percent') == null ){
            $rules['amount'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('app.coupon').' '.__('app.code').' '.__('errors.fieldRequired'),
        ];
    }

}
