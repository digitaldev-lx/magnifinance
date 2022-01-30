@extends('layouts.front')

@push('styles')
    <link href="{{ asset('front/css/login-register.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- RESET PASSWORD SECTION START -->
    <section class="booking_step_section">
        <div class="container">
            <div class="row">
                <div class="col-12 booking_step_heading text-center">
                    <h1>@lang('app.resetPassword')</h1>
                </div>


                <div class="form_wrapper mx-auto position-relative">

                    @if (session('status'))
                        <div class="alert alert-success alert-dismissable" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="post">
                        @csrf

                        <span class="form_icon"><i class="zmdi zmdi-key"></i></span>

                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required autofocus placeholder="@lang('app.email')*" id="username" aria-describedby="username">
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between sendLinkBtn">
                            <button type="submit" class="btn btn-dark btn-blue mb-3 mx-auto w-100">@lang('app.sendPassResetLink')</button>
                        </div>

                        <div class="d-flex justify-content-between backToHome">
                            <a href="{{ route('front.index') }}" class="btn-xs mx-auto d-block">@lang('front.navigation.backToHome')</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- RESET PASSWORD SECTION END -->
@endsection
