@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/booking-step-2.css') }} " rel="stylesheet">
@endpush

@section('content')
    <section class="booking_step_section">
        <div class="container">
            <div class="row ">
                <div class="mx-auto step_2_booking_summary">
                    <div class="alert alert-success">
                        @lang('email.emailVerified').
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
