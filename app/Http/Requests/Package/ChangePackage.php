<?php

namespace App\Http\Requests\Package;

use App\GlobalSetting;
use Illuminate\Foundation\Http\FormRequest;

class ChangePackage extends FormRequest
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
        $dateFormat = GlobalSetting::select('id', 'date_format')->first()->date_format;

        return [
            'amount' => 'required',
            'payDate' => 'required|date_format:"'.$dateFormat.'"',
            'licenceExpireDate' => 'required|date_format:"'.$dateFormat.'"',
        ];
    }

}
