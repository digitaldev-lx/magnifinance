<?php

namespace App\Http\Controllers\Front;

use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GatewayAccountDetail;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PaymentGatewayCredentials;
use App\Models\Tout;
use App\Notifications\BookingConfirmation;
use App\Notifications\CompanyUpdatedPlan;
use App\Notifications\NewBooking;
use App\Notifications\ToutCompanyInfo;
use App\Notifications\ToutPurchased;
use App\Services\StripeCustomerManager;
use App\User;
use Carbon\Carbon;
use DigitalDevLX\Magnifinance\facades\Magnifinance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Stripe;

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

        return Reply::successWithData(__('messages.createdSuccessfully'), ['details' => $account_links, 'link_expire_at' => $link_expire_at/*, 'stripe_login_link' => $stripe_login_link*/]);
    }

    /**
     * Store a details of payment with paypal.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentWithStripe(Request $request)
    {
//        $tax_amount = Tax::active()->first();
        $paymentCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        if (isset($request->booking_id)) {
            $booking = Booking::with('items')->whereId($request->booking_id)->first();

            $stripeAccountDetails = GatewayAccountDetail::activeConnectedOfGateway('stripe')->first();
            $line_items = [];
            foreach ($booking->items as $key => $value) {

                if ($value->businessService->tax_on_price_status == 'inactive') {
                    $price = ($value->business_service_id == null) ?
                        $value->unit_price * 100 :
                        ($value->unit_price * $value->businessService->taxServices[0]->tax->percent) + $value->unit_price * 100;
                } else {
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
                    'success_url' => route('front.afterStripePayment', ['return_url' => $request->return_url, 'booking_id' => $booking->id]),
                    'cancel_url' => route('front.payment-gateway'),
                ];
            } elseif ($destination == null && $destination == '') {
                $data = [
                    'payment_method_types' => ['card'],
                    'line_items' => [$line_items],
                    'success_url' => route('front.afterStripePayment', ['return_url' => $request->return_url]),//todo: possivelmente tem que se passar parametro para indocar o plano
                    'cancel_url' => route('front.payment-gateway'),
                ];
            }

        } elseif (isset($request->tout_id)) {
            $tout = Tout::find($request->tout_id);

            $line_items[] = [
                'name' => __('app.tout') . ' ' . __('app.from') . ' ' . $tout->from . ' ' . __('app.to') . ' ' . $tout->to,
                'amount' => round(currencyConvertedPrice(company()->id, $tout->amount * 100), 2),
                'currency' => $this->settings->currency->currency_code,
                'quantity' => 1,
            ];

            $data = [
                'payment_method_types' => ['card'],
                'line_items' => [$line_items],
                'success_url' => route('front.afterStripePayment', ['return_url' => $request->return_url, 'tout_id' => $tout->id]),
                'cancel_url' => route('front.payment-gateway'),
            ];

        } elseif (isset($request->plan_id)) {

            try {
                DB::beginTransaction();

                $plan = Package::find($request->plan_id);
                $customer_id = (new StripeCustomerManager())->handleCustomerId();

                $data = [
                    'customer' => $customer_id,
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price' => $plan->{'stripe_' . $request->type . '_plan_id'},
//                    'amount' => $plan->{$request->type . '_price'},
//                    'currency' => $this->settings->currency->currency_code,
                        'quantity' => 1,
                    ]],
                    'mode' => 'subscription',
                    'success_url' => route('front.afterStripePayment', ['return_url' => $request->return_url, 'plan_id' => $plan->id, 'type'=> $request->type]),
                    'cancel_url' => route('front.payment-gateway'),
                ];
                $session = \Stripe\Checkout\Session::create($data);

                session(['stripe_session' => $session]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(["success" => false, "message" => $e->getMessage()]);
                abort_and_log(403, $e->getMessage());
            }

        }



        return Reply::dataOnly(['id' => $session->id]);
    }

    public function afterStripePayment(Request $request, $return_url, $bookingId = null)
    {

        if(session()->has('stripe_session')){
            $session_data = session('stripe_session');
            $session = \Stripe\Checkout\Session::retrieve($session_data->id);
        }elseif(isset($request->booking_id)){
            $invoice = Booking::where(['id' => $request->booking_id])->first();
            $session = \Stripe\Checkout\Session::retrieve($invoice->stripe_session_id);
        }

        if (!isset($request->plan_id)) {
            $payment_method = \Stripe\PaymentIntent::retrieve(
                $session->payment_intent,
                []
            );
        }

        if (isset($request->tout_id)) {
            $tout = Tout::whereId($request->tout_id)->first();
        }elseif(isset($request->booking_id) && session()->has('stripe_session')) {
            $invoice = Booking::where(['id' => $request->booking_id, 'user_id' => Auth::user()->id])->first();
        }

        $saCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

//        $currency = GlobalSetting::first()->currency;
        if (isset($request->booking_id)) {
            $payment = new Payment();
            $payment->booking_id = $invoice->id;
            $payment->company_id = $invoice->company_id;
            $payment->currency_id = $invoice->currency_id;
            $payment->customer_id = $this->user->id;
            $payment->amount = $invoice->amount_to_pay;
            $payment->gateway = 'Stripe';
            $payment->transaction_id = $payment_method->id;
            $payment->transfer_status = 'not_transferred';

            if ($payment_method->transfer_data && !is_null($payment_method->transfer_data->destination)) {
                /** @phpstan-ignore-line */
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

            if($invoice->company->magnifinance_active){
                $invoice->emitDocument();
            }

            $formatted_amount = $invoice->formated_amount_to_pay;

            // send email notifications
            $admins = User::allAdministrators()->where('company_id', $invoice->company_id)->first();
            Notification::send($admins, new NewBooking($invoice));

            $user = User::findOrFail($invoice->user_id);
            $user->notify(new BookingConfirmation($invoice));


        } elseif (isset($request->plan_id)) {

            $token = $payment_method = \Stripe\PaymentIntent::retrieve(
                $session->payment_intent,
                []
            );

            $plan = Package::find($request->plan_id);
            $company = $this->company = company();
//            $email = $this->company->company_email;

            $allInvoices = DB::table('stripe_invoices')
                ->join('packages', 'packages.id', 'stripe_invoices.package_id')
                ->selectRaw('stripe_invoices.id , "Stripe" as method, stripe_invoices.pay_date as paid_on ,stripe_invoices.next_pay_date')
                ->whereNotNull('stripe_invoices.pay_date')
                ->where('stripe_invoices.company_id', $company->id)->get();

            /*$firstInvoice = $allInvoices->sortByDesc(function ($temp, $key) {
                return Carbon::parse($temp->paid_on)->getTimestamp();
            })->first();*/

            $subscription = $company->subscriptions;

            try {
                DB::beginTransaction();
                $stripe = new StripeCustomerManager();
                $email = $stripe->getStripeCustomer()->email;
                if ($subscription->count() > 0) {
                    $company->subscription('main')->noProrate()->swap($plan->{'stripe_' . $request->type . '_plan_id'});
                } else {

                    $company->newSubscription('main', $plan->{'stripe_' . $request->type . '_plan_id'})->create($token, [
                        'email' => $email
                    ]);

                }
//                $company = $this->company;

                $company->package_id = $plan->id;
                $company->package_type = $request->type;

                // Set company status active
                $company->status = 'active';
                $company->licence_expire_on = null;
                $company->save();

                if($company->magnifinance_active){
                    $plan->emitDocument($company);
                }

                DB::commit();
                // Send notification to admin & superadmin
                $generatedBy = User::withoutGlobalScopes()->whereNull('company_id')->first();
                $allAdmins = User::allAdministrators()->where('company_id', $company->id)->get();
                Notification::send($generatedBy, new CompanyUpdatedPlan($company, $plan->id));
                Notification::send($allAdmins, new CompanyUpdatedPlan($company, $plan->id));

                Session::put('success', 'Plan has been subscribed.');
                return Redirect::route('admin.billing.index');
            } catch (IncompletePayment $exception) {
                DB::rollBack();
                return redirect()->route(
                    'cashier.payment',
                    [$exception->payment->id, 'redirect' => route('admin.billing.index')]/** @phpstan-ignore-line */
                );
            }

            return Reply::redirect(route('admin.billing.index'), 'Plan has been subscribed');

        } else {
            $tout->transaction_id = $payment_method->id;

            $tout->paid_on = Carbon::now();
            $tout->status = $payment_method->status == 'succeeded' ? 'completed' : 'pending';

            $tout->save();

            if($tout->company->magnifinance_active){
                $tout->emitDocument();
            }

            $formatted_amount = $tout->formated_amount_to_pay;

            $admins = User::allAdministrators()->where('company_id', $tout->company_id)->first();
            $superadmins = User::notCustomer()->withoutGlobalScopes()->whereNull('company_id')->get();
            Notification::send($admins, new ToutCompanyInfo($tout));
            Notification::send($superadmins, new ToutPurchased($tout));
        }

        Session::put('success', __('messages.paymentSuccessAmount') . $formatted_amount);

        if ($return_url == 'bookingPage') {

            return redirect()->route('admin.bookings.index');

        } elseif ($return_url == 'calendarPage') {

            return redirect()->route('admin.calendar');

        } elseif ($return_url == 'toutes') {

            return redirect()->route('admin.toutes.show', $tout->id);
        }
        elseif ($return_url == 'packages') {

            return redirect()->route('admin.billing.index');
        }
        elseif ($return_url == 'POSPayment') {

            return redirect()->route('admin.dashboard');
        }

        if (isset($request->booking_id)) {
            return $this->redirectToPayment($request->booking_id, null, 'Payment success');

        }
        return $this->redirectToPayment(null, $request->tout_id, 'Payment success');

    }

    public function redirectToPayment($bookingId = null, $toutId = null, $message)
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
