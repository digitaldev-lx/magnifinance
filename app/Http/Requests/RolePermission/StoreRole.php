<?php

namespace App\Http\Requests\RolePermission;

use App\Http\Requests\CoreRequest;

class StoreRole extends CoreRequest
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
        $this->request->add(['name' => str_slug($this->display_name, '_')]);

        return [
            'display_name' => 'required|unique:roles,display_name,'.request('role_permission'),
        ];
    }

    public function attributes()
    {
        return [
        'display_name' => 'Role Name',
        ];
    }

}
