<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Admin\BookingNotification\Store;
use App\Models\BookingNotification;

;

class BookingNotificationController extends AdminBaseController
{

    public function store(Store $request)
    {
        $company = company();
        BookingNotification::where('company_id', $company->id)->delete();

        foreach ($request->duration as $key => $duration) {
            $booking = new BookingNotification();
            $booking->company_id = $company->id;
            $booking->duration = $duration;
            $booking->duration_type = $request->duration_type[$key];
            $booking->save();
        }

        return Reply::success(__('messages.googleCalendarNotificationSaved'));
    }

    public function destroy($id)
    {
        $bookingNotification = BookingNotification::findOrFail($id);
        $bookingNotification->delete();
        return Reply::success(__('messages.googleCalendarNotificationDeleted'));
    }

}
