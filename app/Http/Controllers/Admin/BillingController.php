<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\User;
use Exception;
use App\Package;
use Carbon\Carbon;
use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Razorpay\Api\Api;
use App\GlobalSetting;
use App\PaypalInvoice;
use App\OfflineInvoice;
use App\PackageModules;
use App\RazorpayInvoice;
use PayPal\Api\Agreement;
use App\OfflinePlanChange;
use PayPal\Rest\ApiContext;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use App\OfflinePaymentMethod;
use App\RazorpaySubscription;
use App\Traits\StripeSettings;
use Laravel\Cashier\Subscription;
use App\PaymentGatewayCredentials;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use PayPal\Api\AgreementStateDescriptor;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\CompanyUpdatedPlan;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\AdminBaseController;
use Laravel\Cashier\Exceptions\IncompletePayment;
use App\Http\Requests\Admin\Billing\OfflinePaymentRequest;

class BillingController extends AdminBaseController
{
    use StripeSettings;

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.bookings'));
    }

    public function index(Request $request)
    {

        $this->nextPaymentDate = '-';
        $this->previousPaymentDate = '-';
        $this->stripeSettings = PaymentGatewayCredentials::withoutGlobalScopes()->first();
//        $this->subscription = Subscription::where('company_id', company()->id)->first();
//        $this->razorPaySubscription = RazorpaySubscription::where('company_id', company()->id)->orderBy('id', 'Desc')->first();
        $this->nextPaymentDate = '-';
        $this->previousPaymentDate = '-';
        $this->subscription = Subscription::where('company_id', company()->id)->first();
        $this->message = '';

        if($request->get('message'))
        {
            $this->message = $request->get('message');
        }

//        $this->razorPaySubscription = RazorpaySubscription::where('company_id', company()->id)->orderBy('id', 'Desc')->first();

        $allInvoices = DB::table('stripe_invoices')
            ->join('packages', 'packages.id', 'stripe_invoices.package_id')
            ->selectRaw('stripe_invoices.id , "Stripe" as method, stripe_invoices.pay_date as paid_on, "" as end_on ,stripe_invoices.next_pay_date, stripe_invoices.created_at')
            ->whereNotNull('stripe_invoices.pay_date')
            ->where('stripe_invoices.company_id', company()->id)->get();

        /*$razorpay = DB::table('razorpay_invoices')
            ->join('packages', 'packages.id', 'razorpay_invoices.package_id')
            ->selectRaw('razorpay_invoices.id , "Razorpay" as method, razorpay_invoices.pay_date as paid_on, "" as end_on ,razorpay_invoices.next_pay_date, razorpay_invoices.created_at')
            ->whereNotNull('razorpay_invoices.pay_date')
            ->where('razorpay_invoices.company_id', company()->id);*/

        /*$allInvoices = DB::table('paypal_invoices')
            ->join('packages', 'packages.id', 'paypal_invoices.package_id')
            ->selectRaw('paypal_invoices.id, "Paypal" as method, paypal_invoices.paid_on, paypal_invoices.end_on,paypal_invoices.next_pay_date,paypal_invoices.created_at')
            ->where('paypal_invoices.status', 'paid')
            ->where('paypal_invoices.company_id', company()->id)
            ->union($stripe)
            ->union($razorpay)
            ->get();*/

        $this->firstInvoice = $allInvoices->sortByDesc(function ($temp, $key) {
            return Carbon::parse($temp->created_at)->getTimestamp();
        })->first();


        if ($this->firstInvoice) {
            if ($this->firstInvoice->next_pay_date)
            {
                /*if ($this->firstInvoice->method == 'Paypal' && $this->firstInvoice !== null && is_null($this->firstInvoice->end_on))
                {
                    $this->nextPaymentDate = Carbon::parse($this->firstInvoice->next_pay_date)->toFormattedDateString();
                }*/

                if ($this->firstInvoice->method == 'Stripe' && $this->subscription !== null && is_null($this->subscription->ends_at))
                {
                    $this->nextPaymentDate = Carbon::parse($this->firstInvoice->next_pay_date)->toFormattedDateString();
                }

               /* if ($this->firstInvoice->method == 'Razorpay' && $this->razorPaySubscription !== null && is_null($this->razorPaySubscription->ends_at))
                {
                    $this->nextPaymentDate = Carbon::parse($this->firstInvoice->next_pay_date)->toFormattedDateString();
                }*/

            }

            if ($this->firstInvoice->paid_on) {
                $this->previousPaymentDate = Carbon::parse($this->firstInvoice->paid_on)->toFormattedDateString();
            }

        }

//        $this->paypalInvoice = PaypalInvoice::where('company_id', company()->id)->orderBy('created_at', 'desc')->first();

//        $this->package = Package::find(company()->package_id);
        return view('admin.billing.index', $this->data);
    }

    public function data()
    {
        $stripe = DB::table('stripe_invoices')
            ->join('packages', 'packages.id', 'stripe_invoices.package_id')
            ->selectRaw('stripe_invoices.id ,stripe_invoices.invoice_id , packages.name as name, "Stripe" as method,stripe_invoices.amount, stripe_invoices.pay_date as paid_on ,stripe_invoices.next_pay_date,stripe_invoices.created_at')
            ->whereNotNull('stripe_invoices.pay_date')
            ->where('stripe_invoices.company_id', company()->id);

        /*$razorpay = DB::table('razorpay_invoices')
            ->join('packages', 'packages.id', 'razorpay_invoices.package_id')
            ->selectRaw('razorpay_invoices.id ,razorpay_invoices.invoice_id , packages.name as name, "Razorpay" as method,razorpay_invoices.amount, razorpay_invoices.pay_date as paid_on ,razorpay_invoices.next_pay_date,razorpay_invoices.created_at')
            ->whereNotNull('razorpay_invoices.pay_date')
            ->where('razorpay_invoices.company_id', company()->id);*/

        $paypal = DB::table('paypal_invoices')
            ->join('packages', 'packages.id', 'paypal_invoices.package_id')
            ->selectRaw('paypal_invoices.id,"" as invoice_id, packages.name as name, "Paypal" as method ,paypal_invoices.total as amount, paypal_invoices.paid_on,paypal_invoices.next_pay_date, paypal_invoices.created_at')
            ->where('paypal_invoices.status', 'paid')
            ->where('paypal_invoices.company_id', company()->id);

        $offline = DB::table('offline_invoices')
            ->join('packages', 'packages.id', 'offline_invoices.package_id')
            ->selectRaw('offline_invoices.id,"" as invoice_id, packages.name as name, "Offline" as method ,offline_invoices.amount as amount, offline_invoices.pay_date as paid_on,offline_invoices.next_pay_date, offline_invoices.created_at')
            ->where('offline_invoices.company_id', company()->id)
            ->union($paypal)
            ->union($stripe)
//            ->union($razorpay)
            ->get();

        $paypalData = $offline->sortByDesc(function ($temp, $key) {
            return Carbon::parse($temp->created_at)->getTimestamp();
        })->all();

        return DataTables::of($paypalData)
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->editColumn('paid_on', function ($row) {
                if (!is_null($row->paid_on))
                {
                    return Carbon::parse($row->paid_on)->format('Y-m-d');
                }

                return '-';
            })
            ->editColumn('next_pay_date', function ($row) {
                if (!is_null($row->next_pay_date))
                {
                    return Carbon::parse($row->next_pay_date)->format('Y-m-d');
                }

                return '-';
            })
            ->addColumn('action', function ($row) {
                if ($row->method == 'Stripe' && $row->invoice_id) {
                    return '<div class="text-right"><a href="' . route('admin.stripe.invoice-download', $row->invoice_id) . '" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a></div>';
                }

                if ($row->method == 'Paypal') {
                    return '<div class="text-right"><a href="' . route('admin.paypal.invoice-download', $row->id) . '" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a></div>';
                }

                if ($row->method == 'Razorpay') {
                    return '<div class="text-right"><a href="' . route('admin.billing.razorpay-invoice-download', $row->id) . '" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a></div>';
                }

                if ($row->method == 'Offline') {
                    return '<div class="text-right"><a href="' . route('admin.billing.offline-invoice-download', $row->id) . '" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a></div>';
                }


                return '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function changePlan()
    {
        $packages = Package::active()->get();
        $allPackageModules = PackageModules::all();
        $offlineMethods = OfflinePaymentMethod::where('status', 'yes')->count();

        return view('admin.billing.change_plan', compact('packages', 'allPackageModules', 'offlineMethods'));
    }

    public function subscribe(Request $request)
    {

        $this->setStripConfigs();
        $token = $request->payment_method;
        $email = $request->stripeEmail;
        $plan = Package::find($request->plan_id);
        $company = $this->company = company();

        $allInvoices = DB::table('stripe_invoices')
            ->join('packages', 'packages.id', 'stripe_invoices.package_id')
            ->selectRaw('stripe_invoices.id , "Stripe" as method, stripe_invoices.pay_date as paid_on ,stripe_invoices.next_pay_date')
            ->whereNotNull('stripe_invoices.pay_date')
            ->where('stripe_invoices.company_id', $company->id)->get();

        /*$razorpay = DB::table('razorpay_invoices')
            ->join('packages', 'packages.id', 'razorpay_invoices.package_id')
            ->selectRaw('razorpay_invoices.id ,"Razorpay" as method, razorpay_invoices.pay_date as paid_on ,razorpay_invoices.next_pay_date')
            ->whereNotNull('razorpay_invoices.pay_date')
            ->where('razorpay_invoices.company_id', company()->id);*/

        /*$allInvoices = DB::table('paypal_invoices')
            ->join('packages', 'packages.id', 'paypal_invoices.package_id')
            ->selectRaw('paypal_invoices.id, "Paypal" as method, paypal_invoices.paid_on,paypal_invoices.next_pay_date')
            ->where('paypal_invoices.status', 'paid')
            ->whereNull('paypal_invoices.end_on')
            ->where('paypal_invoices.company_id', $company->id)
            ->union($stripe)
//            ->union($razorpay)
            ->get();*/

        $firstInvoice = $allInvoices->sortByDesc(function ($temp, $key) {
            return Carbon::parse($temp->paid_on)->getTimestamp();
        })->first();

        $subcriptionCancel = true;

        if (!is_null($firstInvoice) && $firstInvoice->method == 'Paypal') {
            $subcriptionCancel = $this->cancelSubscriptionPaypal();
        }

        if ($subcriptionCancel)
        {

            $subscription = $company->subscriptions;

            try {
                DB::beginTransaction();
                if ($subscription->count() > 0)
                {
                    $company->subscription('main')->noProrate()->swap($plan->{'stripe_' . $request->type . '_plan_id'});
                }else {
                    if(Str::contains($company->stripe_id, 'acct_')){
                        $connect_id = $company->stripe_id;
                        $company->stripe_id = null;
                    }

                    $company->newSubscription('main', $plan->{'stripe_' . $request->type . '_plan_id'})->create($token, [
                        'email' => $email
                    ]);

                    $company->stripe_id = $company->stripe_id ?? $connect_id;
                }
                $company = $this->company;

                $company->package_id = $plan->id;
                $company->package_type = $request->type;

                // Set company status active
                $company->status = 'active';
                $company->licence_expire_on = null;
                $company->save();
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
                    [$exception->payment->id, 'redirect' => route('admin.billing.index')] /** @phpstan-ignore-line */
                );
            }
        }

        return Reply::redirect(route('admin.billing.index'), 'Plan has been subscribed');
    }

    public function cancelSubscriptionPaypal()
    {
        $credential = PaymentGatewayCredentials::withoutGlobalScopes(['company'])->first();
        $paypal_conf = Config::get('paypal');
        $api_context = new ApiContext(new OAuthTokenCredential($credential->paypal_client_id, $credential->paypal_secret));
        $api_context->setConfig($paypal_conf['settings']);

        $paypalInvoice = PaypalInvoice::whereNotNull('transaction_id')->whereNull('end_on')
            ->where('company_id', company()->id)->where('status', 'paid')->first();

        if ($paypalInvoice) {
            $agreementId = $paypalInvoice->transaction_id;
            $agreement = new Agreement();

            $agreement->setId($agreementId);
            $agreementStateDescriptor = new AgreementStateDescriptor();
            $agreementStateDescriptor->setNote('Cancel the agreement');

            try {
                DB::beginTransaction();
                $agreement->cancel($agreementStateDescriptor, $api_context);
                $cancelAgreementDetails = Agreement::get($agreement->getId(), $api_context);

                // Set subscription end date
                $paypalInvoice->end_on = Carbon::parse($cancelAgreementDetails->agreement_details->final_payment_date)->format('Y-m-d H:i:s');
                $paypalInvoice->save();
                DB::commit();
            } catch (Exception $ex) {
                DB::rollBack();
                return false;
            }

            return true;
        }
    }

    public function selectPackage(Request $request, $packageID)
    {

        $this->setStripConfigs();

        $this->free = false;

        $this->package = Package::findOrFail($packageID);

        if((!round($this->package->monthly_price) > 0 && $this->package->default == 'no' ) || $this->package->is_free == 1  )
        {
            $this->free = true;
        }
        $this->company = company();
        $this->type    = $request->type;
        $this->stripeSettings = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        $this->intent = $this->stripeSettings->stripe_client_id != null && $this->stripeSettings->stripe_secret != null && $this->stripeSettings->stripe_status == 'active' ? $this->company->createSetupIntent() : [];

        $this->logo = $this->company->logo_url;
        $this->countries = Country::all();
        $this->methods = OfflinePaymentMethod::withoutGlobalScope(CompanyScope::class)->where('status', 'yes')->get();

        return View::make('admin.billing.payment-method-show', $this->data);
    }

    public function download(Request $request, $invoiceId)
    {
        $this->setStripConfigs();
        $this->company = company();
        return $this->company->downloadInvoice($invoiceId, [
            'vendor'  => $this->company->company_name,
            'product' => $this->company->package->name,
            'global' => GlobalSetting::first(),
            'logo' => $this->company->logo,
        ]);
    }

    public function offlineInvoiceDownload($id)
    {
        $this->invoice = OfflineInvoice::with(['company', 'package'])->findOrFail($id);
        $pdf = app('dompdf.wrapper');
        $this->generatedBy = $this->superadmin;
        $this->company = company();
        $this->global = $this->superadmin;
        $pdf->loadView('offline-invoice.invoice-1', $this->data);
        $filename = $this->invoice->pay_date->format($this->global->date_format) . '-' . $this->invoice->next_pay_date->format($this->global->date_format);
        return $pdf->download($filename . '.pdf');
    }

    public function offlinePayment(Request $request)
    {
        $this->package_id = $request->package_id;
        $this->offlineId = $request->offlineId;
        $this->type = $request->type;

        return \view('admin.billing.offline-payment', $this->data);
    }

    public function offlinePaymentSubmit(OfflinePaymentRequest $request)
    {
        $checkAlreadyRequest = OfflinePlanChange::where('company_id', company()->id)->where('status', 'pending')->first();

        if ($checkAlreadyRequest) {
            return Reply::error('You have already raised a request.');
        }

        $package = Package::find($request->package_id);

        try {
            DB::beginTransaction();
            // create offline invoice
            $offlineInvoice = new OfflineInvoice();
            $offlineInvoice->package_id = $request->package_id;
            $offlineInvoice->package_type = $request->type;
            $offlineInvoice->offline_method_id = $request->offline_id;
            $offlineInvoice->amount = $request->type == 'monthly' ? $package->monthly_price : $package->annual_price;
            $offlineInvoice->pay_date = Carbon::now()->format('Y-m-d');
            $offlineInvoice->next_pay_date = $request->type == 'monthly' ? Carbon::now()->addMonth()->format('Y-m-d') : Carbon::now()->addYear()->format('Y-m-d');
            $offlineInvoice->save();

            // create offline plan change request
            $offlinePlanChange = new OfflinePlanChange();
            $offlinePlanChange->package_id = $request->package_id;
            $offlinePlanChange->package_type = $request->type;
            $offlinePlanChange->company_id = company()->id;
            $offlinePlanChange->invoice_id = $offlineInvoice->id;
            $offlinePlanChange->offline_method_id = $request->offline_id;
            $offlinePlanChange->description = $request->description;

            if ($request->hasFile('slip')) {
                $offlinePlanChange->file_name = Files::upload($request->slip, 'offline-payment-files', null, null, false);
            }

            $offlinePlanChange->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }


        return Reply::redirect(route('admin.billing.index'));
    }

//    public function razorpayPayment(Request $request)
//    {
//        $credential = PaymentGatewayCredentials::withoutGlobalScopes(['company'])->first();
//
//        $apiKey    = $credential->razorpay_key;
//        $secretKey = $credential->razorpay_secret;
//
//        $paymentId = request('paymentId');
//        $razorpaySignature = $request->razorpay_signature;
//        $subscriptionId = $request->subscription_id;
//        $this->company = company();
//        $api = new Api($apiKey, $secretKey);
//
//        $plan = Package::with('currency')->find($request->plan_id);
//        $type = $request->type;
//
//        $expectedSignature = hash_hmac('sha256', $paymentId . '|' . $subscriptionId, $secretKey);
//
//        if ($expectedSignature === $razorpaySignature) {
//
//            try {
//                $api->payment->fetch($paymentId);
//
//                $payment = $api->payment->fetch($paymentId); // Returns a particular payment
//
//                if ($payment->status == 'authorized') {
//                    $payment->capture(array('amount' => $payment->amount, 'currency' => $plan->currency->currency_code));
//                }
//
//                $company = $this->company;
//
//                $company->package_id = $plan->id;
//                $company->package_type = $type;
//
//                // Set company status active
//                $company->status = 'active';
//                $company->licence_expire_on = null;
//
//                $company->save();
//
//                $subscription = new RazorpaySubscription();
//
//                $subscription->subscription_id = $subscriptionId;
//                $subscription->company_id      = company()->id;
//                $subscription->razorpay_id     = $paymentId;
//                $subscription->razorpay_plan   = $type;
//                $subscription->quantity        = 1;
//                $subscription->save();
//
//                // Send superadmin notification
//                $generatedBy = User::withoutGlobalScopes(['company', 'active'])->whereNull('company_id')->first();
//                $allAdmins = User::allAdministrators()->where('company_id', $company->id)->get();
//                Notification::send($generatedBy, new CompanyUpdatedPlan($company, $plan->id));
//                Notification::send($allAdmins, new CompanyUpdatedPlan($company, $plan->id));
//
//                return Reply::redirect(route('admin.billing.index'), 'Payment successfully done.');
//            } catch (\Exception $e) {
//                return back()->withError($e->getMessage())->withInput();
//            }
//        }
//    }

//    public function razorpaySubscription(Request $request)
//    {
//        $credential = PaymentGatewayCredentials::withoutGlobalScopes(['company'])->first();
//
//        $plan = Package::find($request->plan_id);
//        $type = $request->type;
//
//        $planID = ($type == 'annual') ? $plan->razorpay_annual_plan_id : $plan->razorpay_monthly_plan_id;
//
//        $apiKey    = $credential->razorpay_key;
//        $secretKey = $credential->razorpay_secret;
//
//        $api        = new Api($apiKey, $secretKey);
//        $subscription  = $api->subscription->create(array('plan_id' => $planID, 'customer_notify' => 1, 'total_count' => 100));
//
//        return Reply::dataOnly(['subscriprion' => $subscription->id]);
//    }

//    public function razorpayInvoiceDownload($id)
//    {
//        $this->invoice = RazorpayInvoice::with(['company', 'currency', 'package'])->findOrFail($id);
//        $this->company = company();
//        $this->global = $this->settings;
//        $pdf = app('dompdf.wrapper');
//        $pdf->loadView('razorpay-invoice.invoice-1', $this->data);
//
//        $filename = $this->invoice->pay_date->format($this->global->date_format) . '-' . $this->invoice->next_pay_date->format($this->global->date_format);
//        return $pdf->download($filename . '.pdf');
//    }

    public function cancelSubscription(Request $request)
    {
        $type = $request->type;
        $credential = PaymentGatewayCredentials::withoutGlobalScopes(['company'])->first();

        if ($type == 'paypal')
        {
            $paypal_conf = Config::get('paypal');
            $api_context = new ApiContext(new OAuthTokenCredential($credential->paypal_client_id, $credential->paypal_secret));
            $api_context->setConfig($paypal_conf['settings']);

            $paypalInvoice = PaypalInvoice::whereNotNull('transaction_id')->whereNull('end_on')
                ->where('company_id', company()->id)->where('status', 'paid')->first();

            if ($paypalInvoice) {
                $agreementId = $paypalInvoice->transaction_id;
                $agreement = new Agreement();
                $paypalInvoice = PaypalInvoice::whereNotNull('transaction_id')->whereNull('end_on')
                    ->where('company_id', company()->id)->where('status', 'paid')->first();

                $agreement->setId($agreementId);
                $agreementStateDescriptor = new AgreementStateDescriptor();
                $agreementStateDescriptor->setNote('Cancel the agreement');

                try {
                    $agreement->cancel($agreementStateDescriptor, $api_context);
                    $cancelAgreementDetails = Agreement::get($agreement->getId(), $api_context);

                    // Set subscription end date
                    $paypalInvoice->end_on = Carbon::parse($cancelAgreementDetails->agreement_details->final_payment_date)->format('Y-m-d H:i:s');
                    $paypalInvoice->save();
                } catch (Exception $ex) {
                    Session::put('error', 'Some error occur, sorry for inconvenient');
                    return Redirect::route('admin.billing.');
                }
            }
        }
        /*elseif ($type == 'razorpay')
        {

            $apiKey    = $credential->razorpay_key;
            $secretKey = $credential->razorpay_secret;
            $api       = new Api($apiKey, $secretKey);

            // Get subscription for unsubscribe
            $subscriptionData = RazorpaySubscription::where('company_id', company()->id)->whereNull('ends_at')->first();

            if ($subscriptionData)
            {
                try {

                    $subscription  = $api->subscription->fetch($subscriptionData->subscription_id);

                    if ($subscription->status == 'active')
                    {

                        // unsubscribe plan
                        $subData = $api->subscription->fetch($subscriptionData->subscription_id)->cancel(['cancel_at_cycle_end' => 1]);

                        // plan will be end on this date
                        $subscriptionData->ends_at = \Carbon\Carbon::createFromTimestamp($subData->current_end)->toDateTimeString();
                        $subscriptionData->save();
                    }

                } catch (Exception $ex) {
                    Session::put('@lang("error.error")', '@lang("error.errorMessage")');
                    return Redirect::route('admin.billing.index');
                }
            }

            return Reply::redirectWithError(route('admin.billing.index'), 'There is no data found for this subscription');
        }*/
        else
        {
            $this->setStripConfigs();
            $company = company();
            $subscription = Subscription::where('company_id', company()->id)->whereNull('ends_at')->first();

            if ($subscription)
            {
                try {
                    $company->subscription('main')->cancel();
                } catch (Exception $ex) {
                    Session::put('@lang("error.error")', '@lang("error.errorMessage")');
                    return Redirect::route('admin.billing.index');
                }
            }
        }

        return Reply::redirect(route('admin.billing.index'), __('messages.unsubscribeSuccess'));
    }

}
