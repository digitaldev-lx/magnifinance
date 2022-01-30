<?php

namespace App\Http\Requests\Setting;

use App\Http\Requests\CoreRequest;

class UpdateSetting extends CoreRequest
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
            'company_name' => 'required',
            'company_email' => 'required|email',
            'company_phone' => 'required',
            'address' => 'required',
            'website' => 'required|url',
            'date_format' => 'required',
            'time_format' => 'required'
        ];
    }

}
