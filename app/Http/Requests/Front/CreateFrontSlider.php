<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class CreateFrontSlider extends FormRequest
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
            'images' => 'required',
            'have_content' => 'required|in:yes,no',
            'slider_content' => 'required_if:have_content,yes',
        ];
    }

    public function messages()
    {
        return [
            'image' => __('messages.front.errors.image'),
            'have_content.required' => __('messages.front.errors.have_content'),
            'slider_content.required_if' => __('messages.front.errors.slider_content'),
        ];
    }

}
