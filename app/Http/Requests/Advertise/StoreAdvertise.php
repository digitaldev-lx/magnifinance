<?php

namespace App\Http\Requests\Advertise;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdvertise extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission('create_advertise');
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required|min:150|max:200',
            'info1' => 'required',
            'call_to_action' => 'required',
            'link' => 'required',
            'category_id' => 'required_without:article_id',
            'article_id' => 'required_without:category_id',
            'from' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:now'
            ],
            'to' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:from'
            ],
            'amount' => 'required',
            'avg_amount' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('app.title').' '.__('errors.fieldRequired'),
            'description.required' => __('app.description').' '.__('errors.fieldRequired'),
            'description.min' => __('errors.description.min'),
            'description.max' => __('errors.description.max'),
            'info1.required' => 'Info1 '.__('errors.fieldRequired'),
            'link.required' => __('app.link').' '.__('errors.fieldRequired'),
            'from.required' => __('app.from').' '.__('errors.fieldRequired'),
            'to.required' => __('app.to').' '.__('errors.fieldRequired'),
            'amount.required' => __('app.amount').' '.__('errors.fieldRequired'),
            'avg_amount.required' => __('app.avgAmount').' '.__('errors.fieldRequired'),
            'call_to_action.required' => __('app.call_to_action').' '.__('errors.fieldRequired')
        ];
    }
}
