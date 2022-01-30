<?php

namespace App\Http\Requests\BookingTime;

use App\Http\Requests\CoreRequest;

class UpdateBookingTime extends CoreRequest
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
        if ($this->has('start_time')) {
            $rules = [
                /*'start_time' => 'required_if:status,enabled|date_format:H:i',
                'end_time' => 'required_if:status,enabled|date_format:H:i',*/
                'status' => 'required|in:enabled,disabled',
                'multiple_booking' => 'required|in:yes,no',
                'max_booking' => 'required_if:multiple_booking,yes|integer|min:0',
                'per_day_max_booking' => 'required|integer|min:0',
                'slot_duration' => 'required_if:status,enabled|integer|min:1'
            ];
        }
        else {
            $rules = [
                'status' => 'required|in:enabled,disabled'
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'start_time.required_if' => __('errors.bookingTime.startTime.requiredIf'),
            'start_time.date_format' => __('errors.bookingTime.startTime.dateFormat'),
            'end_time.date_format' => __('errors.bookingTime.endTime.dateFormat'),
            'end_time.required_if' => __('errors.bookingTime.endTime.requiredIf'),
            'slot_duration.required_if' => __('errors.bookingTime.slotDuration.requiredIf'),
            'slot_duration.integer' => __('errors.bookingTime.slotDuration.integer'),
            'slot_duration.min' => __('errors.bookingTime.slotDuration.min'),
            'max_booking.required_if' => __('errors.bookingTime.maxBooking.requiredIf'),
            'max_booking.integer' => __('errors.bookingTime.maxBooking.integer'),
            'max_booking.min' => __('errors.bookingTime.maxBooking.min'),
            'per_day_max_booking.required' => __('errors.bookingTime.perDayMaxBooking.required'),
            'per_day_max_booking.integer' => __('errors.bookingTime.perDayMaxBooking.integer'),
            'per_day_max_booking.min' => __('errors.bookingTime.perDayMaxBooking.min'),
        ];
    }

}
