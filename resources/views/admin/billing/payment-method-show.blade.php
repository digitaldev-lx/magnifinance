<style>
    .stripe-button-el{
        display: none;
    }
    .displayNone {
        display: none;
    }
    .checkbox-inline, .radio-inline {
        vertical-align: top !important;
    }
    .payment-type {
        border: 1px solid #e1e1e1;
        padding: 20px;
        background-color: #f3f3f3;
        border-radius: 10px;
    }
    .box-height {
        height: 78px;
    }
    .button-center {
        display: flex;
        justify-content: center;
    }
    .paymentMethods{display: none; transition: 0.3s;}
    .paymentMethods.show{display: block;}

    .stripePaymentForm{display: none; transition: 0.3s;}
    .stripePaymentForm.show{display: block;}
    .authorizePaymentForm{display: none; transition: 0.3s;}
    .authorizePaymentForm.show{display: block;}

    div#card-element {
        width: 100%;
        color: #4a5568;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        line-height: 1.25;
        border-width: 1px;
        border-radius: 0.25rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        /*appearance: none;*/
        border-style: solid;
        border-color: #e2e8f0;
    }
    .paystack-form {
        display: inline-block;
        position: relative;
    }
    .payment-type {
        margin: 0 5px;
        width: 100%;
    }
    .payment-type button{
        margin: 5px 5px;
        float: none;
    }
    .d-webkit-inline-box {
        display: inline;
    }
    .displayNone {
        display: none;
    }
    #stripe-client-details {
        margin-bottom:20px;
    }
    #stripe-pay-btn {
        margin-top: 15px;
        text-align: center;
    }
</style>

<div id="event-detail">
    <div class="modal-header">
        <h4 class="modal-title">@lang('modules.payments.choosePaymentMethod')</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-body">
            @if(!$free)
                <div class="row paymentMethods show">
                    <div class="col-12 col-sm-12 mt-40 text-center">
                        <div class="form-group">
                            <div class="radio-list">
                                @if($stripeSettings->show_payment_options == 'show')
                                    <label class="radio-inline p-0">
                                        <div class="radio radio-info">
                                            <input checked onchange="showButton('online')" type="radio" name="method" id="radio13" value="high">
                                            <label for="radio13">@lang('app.online')</label>
                                        </div>
                                    </label>
                                @endif
                                @if($methods->count() > 0)
                                    <label class="radio-inline">
                                        <div class="radio radio-info">
                                            <input type="radio" @if(!$stripeSettings->show_pay) checked @endif onchange="showButton('offline')" name="method" id="radio15">
                                            <label for="radio15">@lang('app.offline')</label>
                                        </div>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 mt-40 text-center" id="onlineBox">
                        <div class="form-group payment-type align-items-center justify-content-center d-flex">

                            @if($stripeSettings->paypal_client_id != null && $stripeSettings->paypal_secret != null && $stripeSettings->paypal_status == 'active')
                                <button type="button" class="btn btn-warning waves-effect waves-light paypalPayment" data-toggle="tooltip" data-placement="top" title="Choose Plan">
                                    <i class="icon-anchor display-small"></i><span>
                                        <i class="fa fa-paypal"></i> @lang('modules.invoices.payPaypal')</span>
                                </button>
                            @endif
                            @if($stripeSettings->stripe_client_id != null && $stripeSettings->stripe_secret != null  && $stripeSettings->stripe_status == 'active')
                                <button type="button" class="btn btn-primary waves-effect waves-light stripePay" data-toggle="tooltip" data-placement="top" title="Choose Plan">
                                    <i class="icon-anchor display-small"></i><span>
                                    <i class="fa fa-cc-stripe"></i> @lang('modules.invoices.payStripe')</span>
                                </button>
                            @endif
                            @if($stripeSettings->razorpay_key != null && $stripeSettings->razorpay_secret != null  && $stripeSettings->razorpay_status == 'active')
                                <button type="button" class="btn btn-info waves-effect waves-light m-l-10 razorpay-subscription" data-toggle="tooltip" data-placement="top" title="Choose Plan">
                                    <i class="icon-anchor display-small"></i><span>
                                        <i class="fa fa-credit-card-alt"></i> @lang('modules.invoices.payRazorpay') </span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 mt-40 text-center">
                        @if($methods->count() > 0)
                            <div class="form-group @if(($stripeSettings->show_pay)) displayNone @endif payment-type" id="offlineBox">
                                <div class="radio-list">
                                    @forelse($methods as $key => $method)
                                        <label class="radio-inline @if($key == 0) p-0 @endif">
                                            <div class="radio radio-info" >
                                                <input @if($key == 0) checked @endif onchange="showDetail('{{ $method->id }}')" type="radio" name="offlineMethod" id="offline{{$key}}"
                                                    value="{{ $method->id }}">
                                                <label for="offline{{$key}}" class="text-info" >
                                                    {{ ucfirst($method->name) }} </label>
                                            </div>
                                            <div class="" id="method-desc-{{ $method->id }}">
                                                {{ $method->description }}
                                            </div>
                                        </label>
                                    @empty
                                    @endforelse
                                </div>
                                <div class="row">
                                    <div class="col-md-12 " id="methodDetail">
                                    </div>

                                    @if(count($methods) > 0)
                                        <div class="col-xs-12">
                                            <button type="button" class="btn btn-info save-offline" id="select-offline" data-package-id="{{ $package->id }}">@lang('app.select')</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($stripeSettings->stripe_client_id != null && $stripeSettings->stripe_secret != null  && $stripeSettings->stripe_status == 'active')
                <div class="row stripePaymentForm">
                    <form id="stripe-form" class="ajax-form m-l-10" action="{{ route('admin.subscribe') }}" method="POST">
                        <input type="hidden" id="name" name="name" value="{{ $user->name }}">
                        <input type="hidden" id="stripeEmail" name="stripeEmail" value="{{ $user->email }}">
                        <input type="hidden" name="plan_id" value="{{ $package->id }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        {{ csrf_field() }}
                        <div class="row" id="stripe-client-details">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('modules.payments.clientName')</label>
                                    <input type="text" required name="clientName" id="clientName" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('modules.payments.line') 1</label>
                                    <input type="text" required name="line1" id="line1" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('modules.payments.city')</label>
                                    <input type="text" required name="city" id="city" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('modules.payments.state')</label>
                                    <input type="text" required name="state" id="state" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('modules.payments.country')</label>
                                    <input type="text" required name="country" id="country" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <small>* @lang('modules.payments.addressCountryMustBeValid') <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank">2-alphabet ISO-3166 code</a></small>
                            </div>
                        </div>
                        <div class="flex flex-wrap mb-6">
                            <label for="card-element" class="block text-gray-700 text-sm font-bold mb-2">
                                @lang('modules.payments.cardInfo')
                            </label>
                            <div id="card-element" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></div>
                            <div id="card-errors" class="text-red-400 text-bold mt-2 text-sm font-medium"></div>
                        </div>
                        <!-- Stripe Elements Placeholder -->
                        <div class="flex flex-wrap mt-6" id="stripe-pay-btn">
                            <button type="button" id="card-button"  data-secret="{{ $intent->client_secret }}"  class="btn btn-success inline-block align-middle text-center select-none border font-bold whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700">
                                <i class="fa fa-cc-stripe"></i> {{ __('Pay') }}
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            @else
                <div class="row">
                    <div class="col-xs-12">
                        @lang('messages.choseFreePlan')
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
            @lang('app.cancel')</button>
    </div>
</div>

<script>
    @if($stripeSettings->stripe_client_id != null && $stripeSettings->stripe_secret != null  && $stripeSettings->stripe_status == 'active')

        const stripe = Stripe('{{ config("cashier.key") }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');
        const cardHolderName = document.getElementById('name');
        const cardButton = document.getElementById('card-button');
        const clientSecret = cardButton.dataset.secret;
        let validCard = false;
        const cardError = document.getElementById('card-errors');

        cardElement.addEventListener('change', function(event) {

            if (event.error) {
                validCard = false;
                cardError.textContent = event.error.message;
            } else {
                validCard = true;
                cardError.textContent = '';
            }
        });
       var form = document.getElementById('stripe-form');

        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();
            var line1 = $('#line1').val();
            var city = $('#city').val();
            var state = $('#state').val();
            var country = $('#country').val();

            const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: { name: cardHolderName.value,
                            address: {
                                line1: line1,
                                city: city,
                                state: state,
                                country: country
                            }
                        }
                    }
                }
            );

            if (error) {
                console.log('error');
                // Display "error.message" to the user...
            } else {
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'payment_method');
                hiddenInput.setAttribute('value', setupIntent.payment_method);
                form.appendChild(hiddenInput);
                form.submit();
                // The card has been verified successfully...
            }
        });

        $('body').on('click', '.stripePay', function (e) {
            e.preventDefault();
            $('.paymentMethods').removeClass('show');
            $('.stripePaymentForm').addClass('show');
            $('.modal-title').text('Enter Your Card Details');
        });
    @endif

    // Payment mode
    function showButton(type){
        if(type == 'online'){
            $('#offlineBox').addClass('displayNone');
            $('#onlineBox').removeClass('displayNone');
        }else{
            $('#offlineBox').removeClass('displayNone');
            $('#onlineBox').addClass('displayNone');
        }
    }

    $('body').on('click', '#select-offline', function() {
        let package_id = $(this).data('package-id');

        let offlineId = $("input[name=offlineMethod]").val();
        $.ajaxModal('#package-offline', '{{ route('admin.billing.offline-payment')}}'+'?package_id='+package_id+'&offlineId='+offlineId+'&type='+'{{ $type }}');
    });

    // redirect on paypal payment page
    $('body').on('click', '.paypalPayment', function(){
        $.easyBlockUI('#package-select-form', 'Redirecting Please Wait...');
        var url = "{{ route('admin.paypal', [$package->id, $type]) }}";
        window.location.href = url;
    });

    //Confirmation after transaction
    $('body').on('click', '.razorpay-subscription', function() {
        var plan_id = '{{ $package->id }}';
        var type = '{{ $type }}';
        $.easyAjax({
            type:'POST',
            url:'{{route('admin.billing.razorpay-subscription')}}',
            data: {plan_id: plan_id,type: type,_token:'{{csrf_token()}}'},
            success:function(response){
                razorpayPaymentCheckout(response.subscriprion)
            }
        })
    })

    function razorpayPaymentCheckout(subscriptionID) {
        var options = {
            "key": "{{ $stripeSettings->razorpay_key }}",
            "subscription_id":subscriptionID,
            "name": "{{$company->companyName}}",
            "description": "{{ $package->description }}",
            "image": "{{ $logo }}",
            "handler": function (response){
                confirmRazorpayPayment(response);
            },
            "notes": {
                "package_id": '{{ $package->id }}',
                "package_type": '{{ $type }}',
                "company_id": '{{ $company->id }}'
            },
        };

        var rzp1 = new Razorpay(options);
        rzp1.open();
    }

    //Confirmation after transaction
    function confirmRazorpayPayment(response) {
        var plan_id = '{{ $package->id }}';
        var type = '{{ $type }}';
        var payment_id = response.razorpay_payment_id;
        var subscription_id = response.razorpay_subscription_id;
        var razorpay_signature = response.razorpay_signature;
        $.easyAjax({
            type:'POST',
            url:'{{route('admin.billing.razorpay-payment')}}',
            data: {paymentId: payment_id,plan_id: plan_id,subscription_id: subscription_id,type: type,razorpay_signature: razorpay_signature,_token:'{{csrf_token()}}'},
            redirect:true,
        })
    }

</script>
