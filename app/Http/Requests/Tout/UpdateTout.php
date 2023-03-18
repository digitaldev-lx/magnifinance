<?php

namespace App\Http\Requests\Tout;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTout extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasPermission(['update_tout', 'manage_tout']);
    }

    public function rules()
    {
        return [

        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
