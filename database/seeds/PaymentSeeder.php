<?php

use App\Booking;
use App\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bookings = Booking::select('id', 'amount_to_pay', 'payment_gateway', 'payment_status', 'date_time')->get();

        foreach ($bookings as $booking) {
            $payment = new Payment();

            $payment->currency_id = 1;
            $payment->booking_id = $booking->id;
            $payment->amount = $booking->amount_to_pay;
            $payment->gateway = $booking->payment_gateway;
            $payment->status = $booking->payment_status;
            $payment->paid_on = $booking->date_time;

            $payment->save();
        }
    }

}
