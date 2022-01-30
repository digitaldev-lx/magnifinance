<?php

namespace App\Http\Requests\Admin\BookingNotification;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
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
            'duration.*' => 'required',
            'duration_type.*' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'duration.*.required' => __('app.duration').' '.__('errors.fieldRequired'),
            'duration_type.*.required' => __('app.durationType').' '.__('errors.fieldRequired'),
        ];
    }

}
