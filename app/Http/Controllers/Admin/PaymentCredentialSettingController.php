<?php

namespace App\Http\Controllers\Admin;

use App\GatewayAccountDetail;
use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Payment\UpdateCredentialSetting;
use App\PaymentGatewayCredentials;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Stripe\Stripe;

class PaymentCredentialSettingController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->stripeCredentials = PaymentGatewayCredentials::first();

        /** setup Stripe credentials **/
        Stripe::setApiKey($this->stripeCredentials->stripe_secret);
        $this->pageTitle = 'Stripe';
    }

    public function refreshLink($id)
    {
        $accountId = GatewayAccountDetail::where('id', $id)->first()->account_id;

        $account_links = \Stripe\AccountLink::create([
            'account' => $accountId,
            'type' => 'account_onboarding',
            'return_url' => route('admin.returnStripeSuccess'),
            'refresh_url' => route('admin.refreshLink', $id),
        ]);

        $expiredAt = Carbon::createFromTimestamp((string)$account_links->created)->addDays(7);

        $updateLink = GatewayAccountDetail::findOrFail($id);
        $updateLink->link_expire_at = $expiredAt;
        $updateLink->link = $account_links->url;
        $updateLink->save();

        return Reply::success(__('messages.newLinkGenerated'));
    }

    public function checkVerificationStatus()
    {
        $stripeAccountId = User::with('company')->where('id', Auth::user()->id)->first();

        $stripeCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        $stripe = new \Stripe\StripeClient($stripeCredentials->stripe_secret);

        $accountStatus = $stripe->accounts->retrieve($stripeAccountId->company->stripe_id, []);

        $updateLink = GatewayAccountDetail::ofGateway('stripe')->first();

        $updateLink->connection_status = $accountStatus->details_submitted ? 'connected' : 'not_connected';
        $updateLink->account_status = $accountStatus->details_submitted ? 'active' : 'inactive';

        $updateLink->update();

        return Redirect::to(route('admin.settings.index'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCredentialSetting $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // @codingStandardsIgnoreLine
    public function update(UpdateCredentialSetting $request, $id)
    {

        if ($request->razorpay_status != 'active' && $request->stripe_status != 'active' && $request->paypal_status != 'active' && $request->offline_payment != 1) {
            return Reply::error(__('messages.paymentActiveRequired'));
        }

        $companyId = User::select('company_id')->where('id', Auth::user()->id)->first()->company_id;
        $paymentGatewayCredentialId = PaymentGatewayCredentials::select('id')->where('company_id', $companyId)->first()->id;

        $credential = PaymentGatewayCredentials::findOrFail($paymentGatewayCredentialId);

        $credential->stripe_client_id = $request->stripe_client_id;
        $credential->stripe_secret = $request->stripe_secret;
        $credential->stripe_webhook_secret = $request->stripe_webhook_secret;
        $credential->stripe_status = $request->stripe_status;
        $credential->paypal_status = $request->paypal_status;
        $credential->offline_payment = $request->offline_payment;
        $credential->show_payment_options = $request->show_payment_options;

        $credential->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function accountLinkForm()
    {
        return view('admin.settings.account-create-form');
    }

}
