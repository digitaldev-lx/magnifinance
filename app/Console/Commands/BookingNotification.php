<?php

namespace App\Console\Commands;

use App\Company;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use App\Notifications\BookingReminder;

class BookingNotification extends Command
{
    use Queueable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a notification before booking.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = Company::active()->cronActive()->with('bookingNotNotify')->get();

        foreach ($companies as $company)
        {
            $bookings = $company->bookingNotNotify->whereIn('status', ['approved'])->whereBetween('date_time', [Carbon::now()->timezone($company->timezone), Carbon::now()->timezone($company->timezone)->addMinutes(convertToMinutes($company->duration, $company->duration_type))]);

            foreach ($bookings as $booking)
            {
                $booking->user->notify(new BookingReminder($booking));
                $booking->update(['notify_at' => Carbon::now()]);
            }
        }
    }

}
