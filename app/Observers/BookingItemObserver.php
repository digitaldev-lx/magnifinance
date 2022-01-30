<?php

namespace App\Observers;

use App\BookingItem;

class BookingItemObserver
{

    public function creating(BookingItem $bookingItem)
    {
        if (company()) {
            $bookingItem->company_id = company()->id;
        }
    }

}
