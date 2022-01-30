<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduct extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'location_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('app.name').' '.__('errors.fieldRequired'),
            'location_id.required' => __('app.location').' '.__('errors.fieldRequired'),
        ];
    }

}
