<?php

namespace App\Http\Requests\RolePermission;

use App\Http\Requests\CoreRequest;

class AddMembers extends CoreRequest
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
            'user_ids' => 'required|array|min:1',
        ];
    }

    public function attributes()
    {
        return [
        'user_ids' => 'members',
        ];
    }

}
