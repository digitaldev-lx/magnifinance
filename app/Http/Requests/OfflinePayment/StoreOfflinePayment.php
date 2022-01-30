<?php

namespace App\Http\Requests\OfflinePayment;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreOfflinePayment extends CoreRequest
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
            'status' => 'required|in:yes,no'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('app.name').' '.__('errors.fieldRequired'),
            'description.required' => __('app.description').' '.__('errors.fieldRequired')
        ];
    }

}
