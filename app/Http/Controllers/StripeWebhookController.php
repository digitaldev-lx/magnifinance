<?php

namespace App\Http\Controllers;

use App\Company;
use App\StripeInvoice;
use App\Subscription;
use App\Traits\StripeSettings;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    use StripeSettings;

    public function saveInvoices(Request $request)
    {
        $this->setStripConfigs();

        $stripeCredentials = config('cashier.webhook.secret');

        Stripe::setApiKey(config('cashier.secret'));

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $stripeCredentials;

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid Payload', 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        $payload = json_decode($request->getContent(), true);
        // If webhook with invoice (Success or Failed)
        if($payload['data']['object']['object'] == 'invoice'){
            if ($payload['type'] == 'invoice.payment_succeeded')
            {
                $planId = $payload['data']['object']['lines']['data'][0]['plan']['id'];
                $customerId = $payload['data']['object']['customer'];

                $amount = $payload['data']['object']['amount_paid'];
                $transactionId = $payload['data']['object']['lines']['data'][0]['id'];

                $invoiceRealId = $payload['data']['object']['id'];
                \Log::debug([$customerId,config('cashier.webhook.secret'), $payload]);

                $company = Company::where('stripe_id', $customerId)->first();

                $package = \App\Package::where(function ($query) use($planId) {
                    $query->where('stripe_annual_plan_id', '=', $planId)
                        ->orWhere('stripe_monthly_plan_id', '=', $planId);
                })->first();

                $planType = 'monthly';

                if($package->stripe_annual_plan_id == $planId){
                    $planType = 'annual';
                }

                if($company) {
                    // Store invoice details
                    $stripeInvoice = new StripeInvoice();
                    $stripeInvoice->company_id = $company->id;
                    $stripeInvoice->invoice_id = $invoiceRealId;
                    $stripeInvoice->transaction_id = $transactionId;
                    $stripeInvoice->amount = $amount / 100;
                    $stripeInvoice->package_id = $package->id;
                    $stripeInvoice->pay_date = \Carbon\Carbon::now()->format('Y-m-d');
                    $stripeInvoice->next_pay_date = \Carbon\Carbon::createFromTimeStamp($company->upcomingInvoice()->next_payment_attempt)->format('Y-m-d');
                    $stripeInvoice->type = 'subscription';
                    $stripeInvoice->save();

                    $company->package_id = $package->id;
                    $company->package_type = $planType;

                    // Set company status active
                    $company->licence_expire_on = null;

                    // Change company status active after payment
                    $company->status = 'active';
                    $company->save();


                    $generatedBy = User::whereNull('company_id')->get();
                    $lastInvoice = StripeInvoice::where('company_id')->first();

                    return response('Webhook Handled', 200);

                }

                return response('Customer not found', 200);
            }
            elseif ($payload['type'] == 'invoice.payment_failed') {
                $customerId = $payload['data']['object']['customer'];

                $company = Company::where('stripe_id', $customerId)->first();
                $subscription = Subscription::where('company_id', $company->id)->first();

                if($subscription){
                    $subscription->ends_at = \Carbon\Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $subscription->save();
                }

                if($company) {

                    $company->licence_expire_on = \Carbon\Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $company->save();

                    return response('Company subscription canceled', 200);
                }

                return response('Customer not found', 200);
            }
        }

        // If webhook with payment_intent (Success or Failed)
        elseif($payload['data']['object']['object'] == 'payment_intent'){
            if ($payload['type'] == 'payment_intent.succeeded')
            {
                \Log::debug([$stripeCredentials, config('cashier.secret'), $payload, $sig_header]);

                $customerId = $payload['data']['object']['customer'];

                $company = Company::where('stripe_id', $customerId)->first();

                if($company){
                    $subscription = Subscription::where('company_id', $company->id)->first();

                    if($subscription){
                        $subscription->stripe_status = 'active';
                        $subscription->save();

                    }

                    return response('Webhook Handled', 200);
                }


                return response('Customer not found', 200);
            }

            elseif ($payload['type'] == 'payment_intent.succeeded')
            {
                $customerId = $payload['data']['object']['customer'];

                $company = Company::where('stripe_id', $customerId)->first();
                $subscription = Subscription::where('company_id', $company->id)->first();

                if($subscription){
                    $subscription->ends_at = \Carbon\Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $subscription->save();
                }

                if($company) {
                    $company->licence_expire_on = \Carbon\Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $company->save();

                    return response('Company subscription canceled', 200);
                }

                return response('Customer not found', 200);
            }
        }
    }

}
