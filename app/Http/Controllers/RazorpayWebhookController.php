<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Currency;
use App\Models\Package;
use App\Models\PaymentGatewayCredentials;
use App\Models\RazorpayInvoice;
use App\Models\RazorpaySubscription;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Razorpay\Api\Api;
use Razorpay\Api\Errors;

class RazorpayWebhookController extends Controller
{

    const PAYMENT_AUTHORIZED        = 'subscription.charged';
    const PAYMENT_FAILED            = 'payment.failed';
    const SUBSCRIPTION_CANCELLED    = 'subscription.cancelled';

    public function saveInvoices(Request $request)
    {

        $credential = PaymentGatewayCredentials::withoutGlobalScopes(['company'])->first();

        $apiKey        = $credential->razorpay_key;
        $secretKey     = $credential->razorpay_secret;
        $secretWebhook = $credential->razorpay_webhook_secret;

        $api  = new Api($apiKey, $secretKey);

        $post = file_get_contents('php://input');

        $requestData = json_decode($post, true);

        if (isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE']) === true)
        {
            $razorpayWebhookSecret = $secretWebhook;

            try
            {
                $api->utility->verifyWebhookSignature($post, /** @phpstan-ignore-line */
                    $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'],
                    $razorpayWebhookSecret);
            }
            catch (Errors\SignatureVerificationError $e)
            {
                return;
            }
            switch ($requestData['event'])
            {
            case self::PAYMENT_AUTHORIZED:
                    return $this->paymentAuthorized($requestData);
            case self::PAYMENT_FAILED:
                    return $this->paymentFailed($requestData);
            case self::SUBSCRIPTION_CANCELLED:
                    return $this->subscriptionCancelled($requestData);
            default:
                    return;
            }
        }

    }

    /**
     * Does nothing for the main payments flow currently
     * @param array $requestData Webook Data
     */
    // @codingStandardsIgnoreLine
    protected function paymentFailed(array $requestData)
    {
        return response('Webhook Handled', 200);
    }

    /**
     * Does nothing for the main payments flow currently
     * @param array $requestData Webook Data
     */
    // @codingStandardsIgnoreLine
    protected function subscriptionCancelled(array $requestData)
    {
        return response('Webhook Handled', 200);
    }

    /**
     * @param array $requestData
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    protected function paymentAuthorized(array $requestData)
    {
        // Order entity should be sent as part of the webhook payload

        $packageId = $requestData['payload']['payment']['entity']['notes']['package_id'];
        $type      = $requestData['payload']['payment']['entity']['notes']['package_type'];
        $companyID = $requestData['payload']['payment']['entity']['notes']['company_id'];

        $plan = Package::find($packageId);
        $company = Company::findOrFail($companyID);

        // If it is already marked as paid, ignore the event
        $razorpayPaymentId = $requestData['payload']['payment']['entity']['id'];
        $credential = PaymentGatewayCredentials::withoutGlobalScopes(['company'])->first();

        $apiKey    = $credential->razorpay_key;
        $secretKey = $credential->razorpay_secret;

        $api = new Api($apiKey, $secretKey);

        $payment = $api->payment->fetch($razorpayPaymentId); /** @phpstan-ignore-line */

            // If the payment is only authorized, we capture it
            // If the merchant has enabled auto capture

        try
            {

            if($company) {

                $invoiceID      = $requestData['payload']['payment']['entity']['invoice_id'];
                $orderID        = $requestData['payload']['payment']['entity']['order_id'];
                $subscriptionID = $requestData['payload']['subscription']['entity']['id'];
                $customerID     = $requestData['payload']['subscription']['entity']['customer_id'];
                $amount         = $requestData['payload']['payment']['entity']['amount'];
                $endTimeStamp   = $requestData['payload']['subscription']['entity']['current_end'];
                $currencyCode   = $requestData['payload']['payment']['entity']['currency'];
                $transactionId  = $requestData['account_id'];
                $endDate        = \Carbon\Carbon::createFromTimestamp($endTimeStamp)->toDateTimeString();

                $currency = Currency::where('currency_code', $currencyCode)->first();

                if ($currency) {
                    $currencyID = $currency->id;
                }
                else{
                    $currencyID = Currency::where('currency_code', 'USD')->first()->id;
                }

                // Store invoice details
                $stripeInvoice = new RazorpayInvoice();
                $stripeInvoice->company_id      = $company->id;
                $stripeInvoice->currency_id     = $currencyID;
                $stripeInvoice->order_id        = $orderID;
                $stripeInvoice->subscription_id = $subscriptionID;
                $stripeInvoice->invoice_id      = $invoiceID;
                $stripeInvoice->transaction_id  = $transactionId;
                $stripeInvoice->amount          = $payment->amount / 100;
                $stripeInvoice->package_id      = $packageId;
                $stripeInvoice->pay_date        = \Carbon\Carbon::now()->format('Y-m-d');
                $stripeInvoice->next_pay_date   = $endDate;
                $stripeInvoice->save();

                $subscription = RazorpaySubscription::where('subscription_id', $subscriptionID)->first();
                $subscription->customer_id = $customerID;
                $subscription->save();

                // Change company status active after payment
                $company->status = 'active';
                $company->save();

                $generatedBy  = User::whereNull('company_id')->get();
                $lastInvoice = RazorpayInvoice::first();

                // Todo:Notification

                return response('Webhook Handled', 200);
            }
        }
        catch (\Exception $e)
            {
            // Capture will fail if the payment is already captured

            $log = array(
                'message'         => $e->getMessage(),
                'payment_id'      => $razorpayPaymentId,
                'event'           => $requestData['event']
            );
            error_log(json_encode($log));
        }

        // Graceful exit since payment is now processed.
        exit;
    }

}
