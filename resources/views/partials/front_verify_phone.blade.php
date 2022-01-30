<style>
    #request-otp {
        font-size: 13px;
    }
    #verify-otp {
        font-size: 13px;
    }
</style>

@if ($smsSettings->nexmo_status == 'active')
    @if (!$user->mobile_verified && !session()->has('verify:request_id'))
        <form method="POST" class="ajax-form" id="request-otp-form">
            @csrf
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="mobile">@lang('app.mobile')</label>
                    </div>
                    <div class="col-md-8">
                        <div class="form-row">
                            <div class="col-sm-4">
                                <select name="calling_code" id="calling_code" class="form-control select2">
                                    @foreach ($calling_codes as $code => $value)
                                        <option value="{{ $value['dial_code'] }}"
                                        @if (!is_null($user) && $user->calling_code)
                                            {{ $user->calling_code == $value['dial_code'] ? 'selected' : '' }}
                                        @endif>{{ $value['dial_code'] . ' - ' . $value['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mobile" name="mobile" value="{{ !is_null($user) && $user->mobile ? $user->mobile : '' }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" id="request-otp" class="btn btn-primary w-100">@lang('app.requestOTP')</button>
                    </div>
                </div>
            </div>
        </form>
    @elseif (session()->has('verify:request_id'))
        <form method="POST" class="ajax-form" id="verify-otp-form">
            @csrf
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="otp">@lang('app.otp')</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP" autofocus autocomplete="off" />
                        <span>
                            <label class="text-danger mx-3" id="demo"></label>
                            <span class="attempts_left"></span>
                        </span>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="verify-otp" class="btn btn-primary w-100">@lang('app.verifyMobile')</button>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endif
