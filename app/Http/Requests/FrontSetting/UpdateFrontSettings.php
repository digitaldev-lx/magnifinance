<?php

namespace App\Http\Requests\FrontSetting;

use App\Http\Requests\CoreRequest;

class UpdateFrontSettings extends CoreRequest
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
            'social_links.*' => 'nullable|url',
            'footer_text' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'social_links.*.url' => 'Please enter proper url format'
        ];
    }

}
