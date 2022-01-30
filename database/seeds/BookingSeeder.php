<?php

use App\Booking;
use App\BusinessService;
use App\Tax;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        if (config('app.env') != 'codecanyon') {
            // Insert some bookings data for company Ist.
            $statuses = ['pending', 'in progress', 'completed', 'canceled'];

            for ($i = 0; $i <= 2; $i++) {
                // Dtart
                $businessServices = BusinessService::where('company_id', '1')->get()->random(2);

                $user = User::where('company_id', 1)->get()->random(1);

                $tax = Tax::active()->first();
                $services = $businessServices;
                $quantity = 1;
                $taxAmount = 0;
                $discount = 0;
                $discountAmount = 0;
                $amountToPay = 0;

                $originalAmount = 0;
                $bookingItems = array();

                foreach ($services as $key => $service) {
                    $amount = ($quantity * $service->discounted_price);

                    $bookingItems[] = [
                        'business_service_id' => $service->id,
                        'quantity' => $quantity,
                        'unit_price' => $service->discounted_price,
                        'amount' => $amount
                    ];

                    $originalAmount = ($originalAmount + $amount);
                }

                if (!is_null($tax) && $tax->percent > 0) {
                    $taxAmount = (($tax->percent / 100) * $originalAmount);
                }

                if ($discount > 0) {

                    if ($discount > 100) {
                        $discount = 100;
                    }

                    $discountAmount = (($discount / 100) * $originalAmount);
                }

                $amountToPay = ($originalAmount - $discountAmount + $taxAmount);
                $amountToPay = round($amountToPay, 2);

                $booking = new Booking();
                $booking->company_id = 1;
                $booking->location_id = 1;
                $booking->user_id = $user[0]->id;
                $booking->date_time = Carbon::now()->format('Y-m-' . rand(1, 30) . ' H:i:s');
                $booking->payment_gateway = 'cash';
                $booking->original_amount = $originalAmount;
                $booking->discount = $discountAmount;
                $booking->status = $statuses[array_rand($statuses, 1)];
                $booking->payment_status = 'pending';
                $booking->source = 'pos';
                $booking->additional_notes = 'It is a long established fact that a reader.';
                $booking->discount_percent = 0;

                if (!is_null($tax)) {
                    $booking->tax_name = $tax->name;
                    $booking->tax_percent = $tax->percent;
                    $booking->tax_amount = $taxAmount;
                }

                $booking->amount_to_pay = $amountToPay;
                $booking->save();


                foreach ($bookingItems as $key => $bookingItem) {
                    $bookingItems[$key]['booking_id'] = $booking->id;
                    $bookingItems[$key]['company_id'] = $booking->company_id;
                }

                DB::table('booking_items')->insert($bookingItems);
                // Eend

            }
        }
    }

}
