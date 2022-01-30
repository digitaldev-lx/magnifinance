<?php

namespace App\Http\Requests\Theme;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreTheme extends CoreRequest
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
            'superadmin.primary_color' => 'required',
            'superadmin.secondary_color' => 'required',
            'superadmin.sidebar_bg_color' => 'required',
            'superadmin.sidebar_text_color' => 'required',
            'administrator.primary_color' => 'required',
            'administrator.secondary_color' => 'required',
            'administrator.sidebar_bg_color' => 'required',
            'administrator.sidebar_text_color' => 'required',
            'customer.primary_color' => 'required',
            'customer.secondary_color' => 'required',
            'customer.sidebar_bg_color' => 'required',
            'customer.sidebar_text_color' => 'required',
        ];
    }

}
