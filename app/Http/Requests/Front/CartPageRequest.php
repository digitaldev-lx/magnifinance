<?php

namespace App\Http\Requests\Front;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class CartPageRequest extends CoreRequest
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
            'bookingDate' => 'required|date|after_or_equal:today',
            'bookingTime' => 'required_with:bookingDate'
        ];
    }

    public function messages()
    {
        return [
            'bookingDate.required' => __('messages.front.errors.selectDate'),
            'bookingTime.required_with' => __('messages.front.errors.selectTime')
        ];
    }

}
