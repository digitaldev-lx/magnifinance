<?php

namespace App\Http\Requests\OfficeLeaves;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'startDate' => 'required|'.$this->date_picker_format,
            'endDate' => 'required|'.$this->date_picker_format.'|after_or_equal:startDate',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('app.title').' '.__('errors.fieldRequired'),
            'startdate.required' => __('app.startDate').' '.__('errors.fieldRequired'),
            'enddate.required' => __('app.endDate').' '.__('errors.fieldRequired'),
            'enddate.after_or_equal ' => 'The end date must be a date after or equal to start date.',
        ];
    }

}
