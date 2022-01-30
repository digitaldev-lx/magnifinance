<?php

namespace App\Http\Controllers\Admin;

use App\BookingTime;
use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use Carbon\Carbon;
use App\Http\Requests\BookingTime\UpdateBookingTime;

class BookingTimeController extends AdminBaseController
{

    public function edit(BookingTime $bookingTime)
    {
        $this->bookingTime = $bookingTime;

        return view('admin.booking-time.edit', $this->data);
    }

    public function update(UpdateBookingTime $request, $id)
    {
        $bookingTime = BookingTime::find($id);

        if ($request->start_time) {
            $bookingTime->start_time = Carbon::createFromFormat($this->settings->time_format, $request->start_time)->format('H:i:s');
        }

        if ($request->end_time) {
            $bookingTime->end_time = Carbon::createFromFormat($this->settings->time_format, $request->end_time)->format('H:i:s');
        }

        if ($request->multiple_booking) {
            $bookingTime->multiple_booking = $request->multiple_booking;
        }

        if ($request->multiple_booking === 'yes') {
            $bookingTime->max_booking = $request->max_booking;
        }

        if ($request->per_day_max_booking != null) {
            $bookingTime->per_day_max_booking = $request->per_day_max_booking;
        }

        if ($request->per_slot_max_booking != null) {
            $bookingTime->per_slot_max_booking = $request->per_slot_max_booking;
        }

        if ($request->slot_duration) {
            $bookingTime->slot_duration = $request->slot_duration;
        }

        $bookingTime->status = $request->status;

        $bookingTime->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
