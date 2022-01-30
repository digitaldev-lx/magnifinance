<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends CoreRequest
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
            'title'      => 'required',
            'code'       => 'required|regex:/(^[A-Za-z0-9]+$)+/|unique:coupons,code',
            'start_time' => 'required',
        ];

        if($this->get('amount') == null && $this->get('percent') == null ){
            $rules['amount'] = 'required';
            $rules['percent'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('app.coupon').' '.__('app.code').' '.__('errors.fieldRequired'),
            'title.amount' => 'Please select at-least one Amount or Percent for discount',
            'title.percent' => 'Please select at-least one Percent or Amount for discount',
        ];
    }

}
