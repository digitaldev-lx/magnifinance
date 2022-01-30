@extends('layouts.front')

@push('styles')
    <style>
        .msg-container {
            margin-top:10%;
            margin-bottom:10%;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <section class="sp-80 bg-w">
            <div class="container msg-container">
                <div class="row">
                    <div class="col-12">
                        <div class="all-title">
                            <h4 class="sec-title mb-3">
                                @lang('front.headings.paymentSuccess')
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="billing-info payment-box success-box">
                    <div class="alert alert-success" role="alert">
                        @lang('front.bookingSuccessful')
                    </div>
                </div>
                <div class="row mt-30">
                    <div class="col-12 text-center">
                        <a href="{{ route('front.index') }}" class="btn btn-custom">
                            <i class="fa fa-home mr-2"></i>
                            @lang('front.navigation.backToHome')</a>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection
