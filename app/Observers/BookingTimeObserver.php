<?php

namespace App\Observers;

use App\Models\BookingTime;
use App\Models\EmployeeSchedule;

class BookingTimeObserver
{

    /**
     * Handle the booking time "updated" event.
     *
     * @param  \App\Models\BookingTime  $bookingTime
     * @return void
     */
    public function updated(BookingTime $bookingTime)
    {
        $bookingDay = BookingTime::where('id', $bookingTime->id)->first();
        $schedule = EmployeeSchedule::where('days', $bookingDay->day)->get();

        if ($bookingTime->isDirty('status')){
            if($bookingTime->status == 'enabled'){

                foreach($schedule as $schedules){
                    $schedules->is_working = 'yes';
                    $schedules->update();
                }
            } else {
                foreach($schedule as $schedules){
                    $schedules->is_working = 'no';
                    $schedules->update();
                }
            }
        }
    }

}
