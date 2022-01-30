
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
                        <form action="{{ route('password.request') }}" method="post">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <span class="form_icon"><i class="zmdi zmdi-key"></i></span>

                            <div class="form-group">
                                <input type="email" name="email" id="email" class="form-control form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required autofocus placeholder="@lang('app.email')*" aria-describedby="username">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-4">
                                <input type="password" id="password" class="form-control form-control-lg {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="@lang('app.password')*" id="Password" aria-describedby="Password">
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-4">
                                <input type="password" id="password_confirmation" class="form-control form-control-lg {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password_confirmation" required placeholder="@lang('app.passwordConfirmation')*" id="password_confirmation" aria-describedby="password_confirmation">
                                @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between sendLinkBtn">
                                <button type="submit" class="btn btn-dark btn-blue mb-3 mx-auto w-100">@lang('app.resetPassword')</button>
                            </div>

                            <div class="d-flex justify-content-between backToHome">
                                <a href="{{ route('login') }}" class="btn-xs mx-auto d-block">@lang('app.signIn')</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- RESET PASSWORD SECTION END -->
    @endsection
