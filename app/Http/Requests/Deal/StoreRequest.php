<?php

namespace App\Http\Requests\Deal;

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
            'title'                 => 'required',
            'slug'                 => 'required|unique:deals,slug',
            'applied_between_dates' => 'required',
            'open_time'             => 'required',
            'close_time'            => 'required',
            'locations'             => 'required',
            'services'              => 'required',
            'discount'              => 'required',
            'discount_type'         => 'required',
            'customer_uses_time'    => 'required',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('app.title').' '.__('errors.fieldRequired'),
            'slug.required' => __('app.slug').' '.__('errors.fieldRequired'),
            'slug.unique' => __('app.slug').' '.__('errors.alreadyTaken'),
            'original_amount.required' => __('messages.the').' '.__('app.originalPrice').' '.__('messages.field_is_required'),
            'discount_amount.required' => __('messages.the').' '.__('app.dealPrice').' '.__('messages.field_is_required'),
        ];
    }

}
