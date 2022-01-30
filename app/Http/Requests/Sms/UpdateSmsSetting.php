<?php

namespace App\Http\Requests\Sms;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSmsSetting extends CoreRequest
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
        if (!$this->has('nexmo_status')) {
            $this->request->add(['nexmo_status' => 'deactive']);
        }

        if (!$this->has('msg91_status')) {
            $this->request->add(['msg91_status' => 'deactive']);
        }

        $rules = [
            'nexmo_key' => 'required_if:nexmo_status,active',
            'nexmo_secret' => 'required_if:nexmo_status,active',
            'nexmo_from' => 'required_if:nexmo_status,active|between:3,18',
            'msg91_key' => 'required_if:msg91_status,active',
            'msg91_from' => 'required_if:msg91_status,acstive',
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nexmo_key.required_if' => __('errors.nexmoKeyRequired'),
            'nexmo_secret.required_if' => __('errors.nexmoSecretRequired'),
            'nexmo_from.required_if' => __('errors.nexmoFromRequired'),
            'msg91_key.required_if' => __('errors.msg91KeyRequired'),
            'msg91_from.required_if' => __('errors.msg91FromRequired'),
        ];
    }

}
