@extends('layouts.master')

@push('head-css')
    <link href="{{asset('front/css/booking-step-4.css')}}" rel="stylesheet">
    <style>

        .adsContainer {
            background: #F0F0F0;
            padding: 28px;
            border-radius: 10px;
            box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16);
            margin-bottom: 2em;
            min-height: 150px;
            line-height: 150px;
        }

        .adsContainer p{
            vertical-align: middle;
            background: rgb(144,238,144);
            padding-left: 3rem;
            padding-right: 3rem;
            font-family: "Roboto", "Helvetica Neue", "Helvetica", "Arial", serif;
            font-size: 2rem;
            color: black;
            font-weight: bold;
            text-align: center;
        }


    </style>
@endpush

@section('content')
<div class="mt-5">
    <div class="row">
        <div class="col-10 col-md-10 col-lg-8 col-xl-8 offset-1 offset-md-1 offset-lg-2 offset-xl-2 adsContainer">
            <p style="width: 100%">{{__('app.toutSuccess')}}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-10 col-md-6 col-lg-4 col-xl-6 offset-1 offset-md-3 offset-lg-4 offset-xl-3">
            <div class="row">
                <div class="col-md-6 col-12 mb-3 pb-lg-0 pb-md-0">
                    <a class="payment_icon_name" href="{{route('admin.toutes.index')}}" id="back">

                        <span class="payment_name">{{__('menu.toutes')}}</span>
                    </a>
                </div>
                <div class="col-md-6 col-12 mb-3 pb-lg-0 pb-md-0">
                    <a class="payment_icon_name" href="{{route('admin.dashboard')}}">

                        <span class="payment_name">{{__('menu.dashboard')}}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
