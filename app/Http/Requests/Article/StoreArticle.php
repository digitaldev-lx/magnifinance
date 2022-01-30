<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticle extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission('create_article');
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
            'slug' => 'required|unique:articles',
            'content' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('app.title').' '.__('errors.fieldRequired'),
            'slug.required' => __('app.slug').' '.__('errors.alreadyTaken'),
            'content.unique' => __('app.content').' '.__('errors.fieldRequired')
        ];
    }
}
