<?php

namespace App\Http\Requests\Spotlight;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\CoreRequest;

class StoreSpotlight extends CoreRequest
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
        $rules = [
            'company' => 'required',
            'deal' => 'required',
            'fromdate' => 'required|'.$this->date_picker_format,
            'todate' => 'required|'.$this->date_picker_format.'|after_or_equal:fromdate',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'company.required' => __('app.company').' '. __('app.name').' '.__('errors.fieldRequired'),
            'deal.required' => __('app.deal').' '. __('app.name').' '.__('errors.fieldRequired'),
            'fromdate.required' => __('report.fromDate').' '.__('errors.fieldRequired'),
            'todate.required' => __('report.toDate').' '.__('errors.fieldRequired'),
            'todate.after_or_equal ' => 'The to date must be a date after or equal to from date.',
        ];
    }

}
