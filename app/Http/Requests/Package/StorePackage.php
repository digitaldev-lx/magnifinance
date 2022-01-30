<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class StorePackage extends FormRequest
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
            'name'                      => 'required',
            'max_employees'             => 'required',
            'max_deals'                 => 'required',
            'max_services'              => 'required',
            'max_roles'                 => 'required',
            'monthly_price'             => 'required',
            'annual_price'              => 'required',
        ];

    }

}
