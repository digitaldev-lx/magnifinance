<?php

namespace App\Gateways;

use App\Booking;
use App\Company;
use App\GlobalSetting;
use App\Helper\Razorpay\LocalApi;
use App\PaymentGatewayCredentials;
use Razorpay\Api\Api;

class Razorpay
{

    public function razorPayCredentials()
    {
        return PaymentGatewayCredentials::withoutGlobalScopes()->first();
    }

    public function api()
    {
        /** setup RazorPay credentials **/
        return new Api($this->razorPayCredentials()->razorpay_key, $this->razorPayCredentials()->razorpay_secret);
    }

    public function createAccount($data)
    {
        $api = new LocalApi($this->razorPayCredentials()->razorpay_key, $this->razorPayCredentials()->razorpay_secret);

        return $api->account->create($data); /** @phpstan-ignore-line */
    }

    public function createOrder($data)
    {
        return $this->api()->order->create($data);
    }

    public function verifyPayment($data)
    {
        $api = new LocalApi($this->razorPayCredentials()->razorpay_key, $this->razorPayCredentials()->razorpay_secret);

        return $api->utility->verifyPaymentSignature($data); /** @phpstan-ignore-line */
    }

    public function fetchAndCapturePayment($paymentId)
    {
        // fetch payment
        $payment = $this->api()->payment->fetch($paymentId);

        $razorpay_response = $payment;
        // Capture payment on razorpay if Not captured
        if ($payment->status != 'captured') {
            $razorpay_response = $payment->capture([
                'amount' => $payment->amount,
                'currency' => $payment->currency
            ]);
        }

        return [
            'payment' => $payment,
            'razorpay_response' => $razorpay_response,
        ];
    }

}
