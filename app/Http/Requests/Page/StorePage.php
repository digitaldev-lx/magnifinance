<?php

namespace App\Http\Requests\Page;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StorePage extends CoreRequest
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
            'title' => 'required',
            'slug' => 'required|unique:pages,slug,'.request('id')
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('app.title').' '.__('errors.fieldRequired'),
            'slug.required' => __('app.slug').' '.__('errors.fieldRequired'),
            'slug.unique' => __('app.slug').' '.__('errors.alreadyTaken')
        ];
    }

}
