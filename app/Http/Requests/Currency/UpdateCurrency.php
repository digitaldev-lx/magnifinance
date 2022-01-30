<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrency extends FormRequest
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
            'currency_name' => 'required',
            'currency_symbol' => 'required',
            'currency_code' => 'required|unique:currencies,currency_code,'.$this->id,
            'exchange_rate' => 'required|numeric|min:1',
        ];
    }

}
