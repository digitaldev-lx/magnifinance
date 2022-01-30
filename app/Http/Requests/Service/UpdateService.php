<?php

namespace App\Http\Requests\Service;

use App\Http\Requests\CoreRequest;
use App\Rules\BusinessServiceUniqueSlug;
use Illuminate\Foundation\Http\FormRequest;

class UpdateService extends CoreRequest
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
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'discount' => 'required',
            'time' => 'required',
            'category_id' => 'required',
            'location_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('app.name').' '.__('errors.fieldRequired'),
            'category_id.required' => __('app.category').' '.__('errors.fieldRequired'),
            'location_id.required' => __('app.location').' '.__('errors.fieldRequired'),
        ];
    }

}
