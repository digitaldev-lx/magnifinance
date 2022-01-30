<?php

namespace App\Http\Requests\Superadmin\SocialAuth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'google_client_id' => 'required_if:google_status,on',
            'google_secret_id' => 'required_if:google_status,on',
            'facebook_client_id' => 'required_if:facebook_status,on',
            'facebook_secret_id' => 'required_if:facebook_status,on',

        ];
    }

}
