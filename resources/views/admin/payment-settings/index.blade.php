@if ($paymentCredential->stripe_status != 'active' && $paymentCredential->razorpay_status != 'active')
    <div class="row alert alert-warning m-0">
        <div class="col-md-12 d-flex align-items-center">@lang('app.superAdminPaymentGatewayMessage') </div>
    </div>
@endif

@if ($paymentCredential->stripe_status == 'active')
    <div class="row">
        <div class="col-md-6">
            <h5 class="text-info">@lang('app.stripe')</h5>
            @if (!$stripePaymentSetting)
                <button id="stripe-get-started" type="button" class="btn btn-success"
                    title="@lang('modules.paymentCredential.connectionDescription')">
                    <i class="fa fa-play"></i> @lang('modules.paymentCredential.getStarted')
                </button>
            @else
                <button id="stripe-get-started" type="button" class="btn btn-success"
                    title="@lang('modules.paymentCredential.connectionsDescription')"
                    disabled>&nbsp;&nbsp;&nbsp;@lang('app.stripe')&nbsp;&nbsp;&nbsp;</button>
            @endif
            <div id="account-id-display" class="form-group @if (!$stripePaymentSetting) d-none @endif">
                <h5 class="text-default">@lang('app.yourAccountId'):
                    <span>{{ $stripePaymentSetting->account_id ?? '' }}</span>
                </h5>
                <h5 class="text-default">{{__('app.login')}}:
                    <a href="{{$stripePaymentSetting->stripe_login_link->url}}" target="_blank">Stripe Login</a>
                </h5>
            </div>
        </div>

        <div class="col-md-3">
            <h5 class="text-info">@lang('app.status')</h5>
            <div class="form-group">
                <span
                    class="badge {{ $stripePaymentSetting && $stripePaymentSetting->connection_status === 'connected' ? 'badge-success' : 'badge-danger' }}">{{ $stripePaymentSetting && $stripePaymentSetting->connection_status === 'connected' ? __('app.connected') : __('app.notConnected') }}</span>
            </div>
        </div>
    </div>
    <br>
    <div id="stripe-verification"
        class="{{ $stripePaymentSetting && $stripePaymentSetting->connection_status === 'not_connected' ? '' : 'd-none' }} row">
        <div class="col-md-12">
            <div class="d-flex">
                <h5 class="text-default mr-3">
                    @lang('app.verificationLink'):
                </h5>
                <a class="mr-3" href="{{ $stripePaymentSetting->link ?? '' }}" target="_blank">
                    {{ $stripePaymentSetting->link ?? '' }}
                </a>
                @if ($stripePaymentSetting && $stripePaymentSetting->link_expire_at->lessThanOrEqualTo(\Carbon\carbon::now()))
                    <button class="btn btn-info btn-sm" type="submit" value="Refresh" id="refreshLink"
                        name="refreshLink"> <i class="fa fa-refresh" aria-hidden="true"></i></button>
                @endif
            </div>
            <div id="linkExpireNote" class="form-text text-muted">
                @lang('app.linkExpireNote'):
                <span>
                    {{ $stripePaymentSetting ? $stripePaymentSetting->link_expire_at->diffForHumans() : '' }}
                </span>
            </div>
        </div>
    </div>
@endif

@if ($paymentCredential->razorpay_status == 'active' && $paymentCredential->stripe_status == 'active')
    <hr>
@endif

@if ($paymentCredential->razorpay_status == 'active')
    <div class="row">
        <div class="col-md-6">
            <h5 class="text-info">@lang('app.razorpay')</h5>
            @if (!$razoypayPaymentSetting && $paymentCredential->razorpay_status == 'active')
                <button id="razorpay-get-started" type="button" class="btn btn-success"
                    title="@lang('modules.paymentCredential.connectionDescription')">
                    <i class="fa fa-play"></i> @lang('modules.paymentCredential.getStarted')
                </button>
            @else
                <button id="razorpay-get-started" type="button" class="btn btn-success"
                    title="@lang('modules.paymentCredential.connectionsDescription')"
                    disabled>@lang('app.razorpay')</button>
            @endif
            <div id="razor-account-id-display" class="form-group @if (!$razoypayPaymentSetting) d-none @endif">
                <h5 class="text-default">@lang('app.yourAccountId'):
                    <span>{{ $razoypayPaymentSetting->account_id ?? '' }}</span>
                </h5>
            </div>
        </div>
        <div id="razor-status" class="col-md-3">
            <h5 class="text-info">@lang('app.status')</h5>
            <div class="form-group">
                <span
                    class="badge {{ $razoypayPaymentSetting && $razoypayPaymentSetting->connection_status === 'connected' ? 'badge-success' : 'badge-danger' }}">{{ $razoypayPaymentSetting && $razoypayPaymentSetting->connection_status === 'connected' ? __('app.connected') : __('app.notConnected') }}</span>
            </div>
        </div>
    </div>
@endif
