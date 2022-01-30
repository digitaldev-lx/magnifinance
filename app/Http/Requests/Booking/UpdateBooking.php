<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\CoreRequest;

class UpdateBooking extends CoreRequest
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
            /**
             * 'cart_services' => 'required|array|min:1',
             *  'cart_quantity' => 'required|array|min:1',
             *  'cart_prices' => 'required|array|min:1',
             *  'booking_date' => 'required', 'booking_time' => 'required'
             */

        ];
    }

}
