<?php

namespace App\Http\Requests\Front;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends CoreRequest
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
            'email' => 'required',
            'details' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.front.errors.name'),
            'email.required' => __('messages.front.errors.email'),
            'details.required' => __('messages.front.errors.details')
        ];
    }

}
