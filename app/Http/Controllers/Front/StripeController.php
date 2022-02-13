<?php

namespace App\Http\Controllers\Front;

use App\Advertise;
use App\Notifications\AdvertiseCompanyInfo;
use App\Notifications\AdvertisePurchased;
use App\User;
use App\Booking;
use App\Payment;
use Carbon\Carbon;
use Stripe\Stripe;
use App\Helper\Reply;
use App\GlobalSetting;
use Illuminate\Http\Request;
use App\GatewayAccountDetail;
use App\Notifications\NewBooking;
use App\PaymentGatewayCredentials;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Notifications\BookingConfirmation;
use App\Tax;
use Illuminate\Support\Facades\Notification;

class StripeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->stripeCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        /** setup Stripe credentials **/
        Stripe::setApiKey($this->stripeCredentials->stripe_secret);
        $this->pageTitle = 'Stripe';
    }

    public function createAccountLink()
    {
        $account = \Stripe\Account::create([
            'country' => 'PT',
            'type' => 'express',
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
        ]);

        $account_links = \Stripe\AccountLink::create([
            'account' => $account->id,
            'type' => 'account_onboarding',
            'return_url' => route('admin.returnStripeSuccess'),
            'refresh_url' => route('admin.refreshLink', $account->id),
        ]);

        $link_expire_at = Carbon::createFromTimestamp($account_links->created)->addDays(7)->diffForHumans();

        $expireDate = Carbon::createFromTimestamp($account_links->created)->addDays(7)->toDateTimeString();

        $company = $this->user->company;
        $company->stripe_id = $account->id;
        $company->save();

        $details = GatewayAccountDetail::where('company_id', $company->id)->first();
        $details = $details ? $details : new GatewayAccountDetail();
        $details->company_id = $company->id;
        $details->account_id = $account->id;
        $details->link = $account_links->url;
        $details->link_expire_at = $expireDate;
        $details->gateway = 'stripe';
        $details->connection_status = 'not_connected';
        $details->save();

        /* $stripe_login_link = \Stripe\Account::createLoginLink($account->id);*/

        return Reply::successWithData(__('messages.createdSuccessfully'), ['details' => $account_links, 'link_expire_at' => $link_expire_at/*, 'stripe_login_link' => $stripe_login_link*/]);
    }

    /**
     * Store a details of payment with paypal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paymentWithStripe(Request $request)
    {
        $tax_amount = Tax::active()->first();
        $paymentCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        if(isset($request->booking_id)){
            $booking = Booking::with('items')->whereId($request->booking_id)->first();
            $stripeAccountDetails = GatewayAccountDetail::activeConnectedOfGateway('stripe')->first();

            $line_items = [];
            foreach ($booking->items as $key => $value) {

                if($value->businessService->tax_on_price_status == 'inactive'){
                    $price = ($value->business_service_id == null) ?
                        $value->unit_price * 100 :
                        ($value->unit_price * $value->businessService->taxServices[0]->tax->percent) + $value->unit_price * 100;
                }else{
                    $price = $value->unit_price * 100;
                }

                $name = ($value->business_service_id == null) ? $value->product->name ?? 'deal' : $value->businessService->name;

                $line_items[] = [
                    'name' => $name,
                    'amount' => round(currencyConvertedPrice($value->company_id, $price), 2),
                    'currency' => $this->settings->currency->currency_code,
                    'quantity' => $value->quantity,
                ];
            }
            $amount = $booking->converted_amount_to_pay * 100;
            $destination = $stripeAccountDetails ? $stripeAccountDetails->account_id : '';

            $applicationFee = round((($amount / 100) * $paymentCredentials->stripe_commission_percentage), 0);
            $data = [];

            if ($destination != null && $destination != '') {
                $data = [
                    'payment_method_types' => ['card'],
                    'line_items' => [$line_items],
                    'payment_intent_data' => [
                        'application_fee_amount' => $applicationFee,
                        'transfer_data' => [
                            'destination' => $destination,
                        ],
                    ],
                    'success_url' => route('front.afterStripePayment',  ['return_url' => $request->return_url, 'booking_id' => $booking->id]),
                    'cancel_url' => route('front.payment-gateway'),
                ];
            }
            elseif ($destination == null && $destination == '') {
                $data = [
                    'payment_method_types' => ['card'],
                    'line_items' => [$line_items],
                    'success_url' => route('front.afterStripePayment', $request->return_url), //todo: possivelmente tem que se passar parametro para indocar o plano
                    'cancel_url' => route('front.payment-gateway'),
                ];
            }

        }elseif (isset($request->advertise_id)){
            $advertise = Advertise::find($request->advertise_id);

            $line_items[] = [
                'name' => __('app.advertise') . ' ' . __('app.from') . ' ' . $advertise->from . ' ' . __('app.to'). ' ' .$advertise->to,
                'amount' => round(currencyConvertedPrice(company()->id, $advertise->amount * 100), 2),
                'currency' => $this->settings->currency->currency_code,
                'quantity' => 1,
            ];

            $data = [
                'payment_method_types' => ['card'],
                'line_items' => [$line_items],
                'success_url' => route('front.afterStripePayment', ['return_url' => $request->return_url, 'advertise_id' => $advertise->id]),
                'cancel_url' => route('front.payment-gateway'),
            ];
        }

        $session = \Stripe\Checkout\Session::create($data);

        session(['stripe_session' => $session]);

        return Reply::dataOnly(['id' => $session->id]);
    }

    public function afterStripePayment(Request $request, $return_url, $bookingId = null)
    {
        $session_data = session('stripe_session');
        $session = \Stripe\Checkout\Session::retrieve($session_data->id);

        $payment_method = \Stripe\PaymentIntent::retrieve(
            $session->payment_intent,
            []
        );

        if (isset($request->advertise_id)) {
            $advertise = Advertise::where(['id' => $request->advertise_id])->first();
        }
        else {
            $invoice = Booking::where(['id' => $request->booking_id, 'user_id' => Auth::user()->id])->first();
        }

        $saCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        $currency = GlobalSetting::first()->currency;

        if(isset($request->booking_id)){
            $payment = new Payment();
            $payment->booking_id = $invoice->id;
            $payment->company_id = $invoice->company_id;
            $payment->currency_id = $invoice->currency_id;
            $payment->customer_id = $this->user->id;
            $payment->amount = $invoice->amount_to_pay;
            $payment->gateway = 'Stripe';
            $payment->transaction_id = $payment_method->id;
            $payment->transfer_status = 'not_transferred';

            if ($payment_method->transfer_data && !is_null($payment_method->transfer_data->destination)) { /** @phpstan-ignore-line */
                $payment->transfer_status = 'transferred';
            }

            $payment->paid_on = Carbon::now();
            $payment->status = $payment_method->status == 'succeeded' ? 'completed' : 'pending';
            $payment->commission = $saCredentials->stripe_commission_status === 'active' ?
                round($invoice->amount_to_pay / 100, 2) * $saCredentials->stripe_commission_percentage : 0;
            $payment->save();

            $invoice->payment_gateway = 'Stripe';
            $invoice->payment_status = 'completed';
            $invoice->save();
            $formatted_amount = $invoice->formated_amount_to_pay;

            // send email notifications
            $admins = User::allAdministrators()->where('company_id', $invoice->company_id)->first();
            Notification::send($admins, new NewBooking($invoice));

            $user = User::findOrFail($invoice->user_id);
            $user->notify(new BookingConfirmation($invoice));
        }else{
            $advertise->transaction_id = $payment_method->id;

            $advertise->paid_on = Carbon::now();
            $advertise->status = $payment_method->status == 'succeeded' ? 'completed' : 'pending';

            $advertise->save();
            $formatted_amount = $advertise->formated_amount_to_pay;

            $admins = User::allAdministrators()->where('company_id', $advertise->company_id)->first();
            $superadmins = User::notCustomer()->withoutGlobalScopes()->whereNull('company_id')->get();
            Notification::send($admins, new AdvertiseCompanyInfo($advertise));
            Notification::send($superadmins, new AdvertisePurchased($advertise));

        }

        Session::put('success', __('messages.paymentSuccessAmount') . $formatted_amount);

        if ($return_url == 'bookingPage') {

            return redirect()->route('admin.bookings.index');

        }elseif ($return_url == 'calendarPage') {

            return redirect()->route('admin.calendar');

        }elseif ($return_url == 'advertises') {

            return redirect()->route('admin.advertises.show', $advertise->id);
        }

        if(isset($request->booking_id)){
            return $this->redirectToPayment($request->booking_id, null, 'Payment success');

        }
        return $this->redirectToPayment(null, $request->advertise_id, 'Payment success');

    }

    public function redirectToPayment($bookingId = null, $advertiseId = null,  $message)
    {
        if ($bookingId == null) {
            return redirect()->route('front.payment.success')->with(['message' => $message]);
        }

        return redirect()->route('front.payment.success')->with(['id' => $bookingId, 'message' => $message]);
    }

    public function redirectToErrorPage($id, $message)
    {

        Session::put('error', __('messages.errorMessage'));

        if ($id == null) {
            return Reply::redirect(route('front.payment.fail'), $message);
        }

        return Reply::redirect(route('front.payment.fail', $id), $message);
    }

}
