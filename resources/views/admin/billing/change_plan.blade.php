@extends('layouts.master')

@push('head-css')
    <style>
        #blank-th-monthly , #blank-th-yearly {
            background:#fff !important;
            min-width:80px;
        }
    </style>
@endpush

@php
    $available_payment_gateways = 0;

    $available_payment_gateways = $paymentCredential->stripe_status=='active' ? $available_payment_gateways+1 : $available_payment_gateways;
    $available_payment_gateways = $paymentCredential->paypal_status=='active' ? $available_payment_gateways+1 : $available_payment_gateways;
    $available_payment_gateways = $paymentCredential->razorpay_status=='active' ? $available_payment_gateways+1 : $available_payment_gateways;

    $width = 30;
    if ($available_payment_gateways==1) { $width = 66; }
    elseif ($available_payment_gateways==2) { $width = 35; }

@endphp

@push('head-css')
    <style>
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }
        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }
        .StripeElement--invalid {
            border-color: #fa755a;
        }
        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
        .alert-warning
        {
            border-color: #fdfdfd00;
            padding: 12px 0px 0px 0px;
        }
        .bg-warning, .alert-warning, .label-warning {
            background-color: #f7f7f7 !important;
        }
        a {
            text-decoration: none;
        }
        .dataTable thead {
            background: #4c5667;
            color: white;
        }
        .text-megna {
            color: #788ae2;
        }
        .table>thead>tr.active>th, .table>thead>tr>td.active, .table>thead>tr>th.active {
            background-color: #f7fafc!important;
        }
        .table thead th {
            border-top: 0;
            border-bottom: none;
            font-weight: 500;
            padding: 1.1rem .45rem;
        }

         .modal {
         text-align: center;
         }

         @media screen and (min-width: 768px) {
            .modal:before {
            display: inline-block;
            vertical-align: middle;
            content: " ";
            height: 100%;
            }
         }

         .modal-dialog {
         display: inline-block;
         text-align: left;
         vertical-align: middle;
         }

        .payment-methods {
            border: 1px solid #d8d4d4;
            padding: 31px;
            margin: 8px;
            max-width: {{ $width }}%;
            text-align: center;
        }

        .payment-methods:hover {
            background: #4c5667;
            color: #f7f7f7;
            cursor: pointer;
        }

        .payment-method-active {
            background: #4c5667;
            color: #f7f7f7;
        }

        .payment-methods label {
         font-size: 18px;
         margin-top: 15px;
         font-weight: 100 !important;
        }

        .font-awesome {
            font-size: 65px;
        }

        .modal-header .close, .modal-header .mailbox-attachment-close {
            padding: 5px;
            margin: 0rem 0rem 0rem auto;
         }

      .select2-container--default.select2-container--focus .select2-selection--multiple {
          border-color: #999;
      }
      .select2-dropdown .select2-search__field:focus, .select2-search--inline .select2-search__field:focus {
          border: 0px;
      }
      .select2-container--default .select2-selection--multiple .select2-selection__rendered {
          margin: 0 13px;
      }
      .select2-container--default .select2-selection--multiple {
          border: 1px solid #cfd1da;
      }
      .select2-container--default .select2-selection--multiple .select2-selection__clear {
          cursor: pointer;
          float: right;
          font-weight: bold;
          margin-top: 8px;
          margin-right: 15px;
      }
  </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header" >
                    <div class="alert alert-warning">
                        <h3>@lang('modules.package.monthlyPackage')</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-responsive-froid">
                       <input type="hidden" id="stripe-id">
                       <input type="hidden" id="razorpay-id">
                        <table class="table text-center">
                           <thead>
                              <tr class="active table-active">
                                 <th id="blank-th-monthly"></th>
                                 @foreach ($packages as $package)
                                    @if (round($package->monthly_price) > 0)
                                       <th>
                                          <center>
                                                <h3>{{ $package->name }} </h3>
                                          </center>
                                       </th>
                                    @endif
                                 @endforeach
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td><br>@lang('modules.invoices.price')</td>
                                 @foreach ($packages as $package)
                                    @if (round($package->monthly_price) > 0)
                                       <td>
                                          <h3 class="panel-title price ">{{$superadmin->currency->currency_symbol}}{{ round($package->monthly_price) }}</h3>
                                       </td>
                                    @endif
                                 @endforeach
                              </tr>
                              <tr>
                                 <td>@lang('modules.package.employees')</td>
                                 @foreach ($packages as $package)
                                    @if (round($package->monthly_price) > 0)
                                       <td>{{ $package->max_employees }} @lang('modules.package.members')</td>
                                    @endif
                                 @endforeach
                              </tr>
                              <tr>
                                 <td>@lang('modules.package.services')</td>
                                 @foreach ($packages as $package)
                                    @if (round($package->monthly_price) > 0)
                                       <td>{{ $package->max_services }}</td>
                                    @endif
                                 @endforeach
                              </tr>
                              <tr>
                                 <td>@lang('modules.package.deals')</td>
                                 @foreach ($packages as $package)
                                    @if (round($package->monthly_price) > 0)
                                       <td>{{ $package->max_deals }}</td>
                                    @endif
                                 @endforeach
                              </tr>
                              <tr>
                                 <td>@lang('modules.package.roleAndPermission')</td>
                                 @foreach ($packages as $package)
                                    @if (round($package->monthly_price) > 0)
                                       <td>{{ $package->max_roles }}</td>
                                    @endif
                                 @endforeach
                              </tr>
                               @foreach ($allPackageModules as $module)
                                    <tr>
                                        <td>{{$module->name}}</td>
                                        @foreach ($packages as $package)
                                            @php
                                                $package_modules = !is_null($package->package_modules) ? json_decode($package->package_modules, true) : [];
                                            @endphp
                                            @if (round($package->monthly_price) > 0)
                                                <td>
                                                    @if (in_array($module->name, $package_modules))
                                                        <i class="fa fa-check text-megna fa-lg"></i>
                                                    @else
                                                        <i class="fa fa-times text-danger fa-lg"></i>
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr>
                                    <td> </td>
                                    @foreach ($packages as $package)
                                        @if (round($package->monthly_price) > 0 && ($offlineMethods > 0 || $paymentCredential->stripe_status=='active'))
                                            <td>
                                                @php
                                                    $is_plan = Illuminate\Support\Str::contains(json_encode(company()->subscriptions), $package->stripe_monthly_plan_id)
                                                @endphp
                                                @if ((!is_null($package->stripe_monthly_plan_id)))
                                                    <button class="btn {{$is_plan ? 'btn-danger' : 'btn-success buy-plan'}}" {{$is_plan ? '' : 'data-toggle="modal" data-target="#myModal"'}} data-package-id="{{ $package->id }}" data-package-type="monthly" data-stripe-id="{{ $package->stripe_monthly_plan_id }}">{{$is_plan ? __('app.currentPlan') : __('app.buy')}}</button>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                           </tbody>
                        </table>
                     </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header" >
                    <div class="alert alert-warning">
                        <h3>@lang('modules.package.yearlyPackages')</h3>
                    </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive table-responsive-froid">
                     <table class="table text-center">
                        <thead>
                           <tr class="active table-active">
                              <th id="blank-th-yearly"></th>
                              @foreach ($packages as $package)
                                 @if (round($package->annual_price) > 0)
                                    <th>
                                       <center>
                                          <h3>{{ $package->name }}</h3>
                                       </center>
                                    </th>
                                 @endif
                              @endforeach
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><br>@lang('modules.invoices.price')</td>
                              @foreach ($packages as $package)
                                 @if (round($package->annual_price) > 0)
                                    <td>
                                       <h3 class="panel-title price ">{{$superadmin->currency->currency_symbol}}{{ round($package->annual_price) }}</h3>
                                    </td>
                                 @endif
                              @endforeach
                           </tr>
                           <tr>
                              <td>@lang('modules.package.employees')</td>
                              @foreach ($packages as $package)
                                 @if (round($package->annual_price) > 0)
                                    <td>{{ $package->max_employees }} @lang('app.members')</td>
                                 @endif
                              @endforeach
                           </tr>
                           <tr>
                              <td>@lang('modules.package.services')</td>
                              @foreach ($packages as $package)
                                 @if (round($package->annual_price) > 0)
                                    <td>{{ $package->max_services }}</td>
                                 @endif
                              @endforeach
                           </tr>
                           <tr>
                              <td>@lang('modules.package.deals')</td>
                              @foreach ($packages as $package)
                                 @if (round($package->annual_price) > 0)
                                    <td>{{ $package->max_deals }}</td>
                                 @endif
                              @endforeach
                           </tr>
                           <tr>
                              <td>@lang('modules.package.roleAndPermission')</td>
                              @foreach ($packages as $package)
                                 @if (round($package->annual_price) > 0)
                                    <td>{{ $package->max_roles }}</td>
                                 @endif
                              @endforeach
                           </tr>
                           @foreach ($allPackageModules as $module)
                           <tr>
                               <td>{{$module->name}}</td>
                               @foreach ($packages as $package)
                                @php
                                    $package_modules = !is_null($package->package_modules) ? json_decode($package->package_modules, true) : [];
                                @endphp
                                  @if (round($package->annual_price) > 0)
                                     <td>
                                        @if (in_array($module->name, $package_modules))
                                           <i class="fa fa-check text-megna fa-lg"></i>
                                        @else
                                           <i class="fa fa-times text-danger fa-lg"></i>
                                        @endif
                                     </td>
                                  @endif
                               @endforeach
                           </tr>
                           @endforeach
                           <tr>
                              <td> </td>
                              @foreach ($packages as $package)
                                    @if (round($package->annual_price) > 0 && ($offlineMethods > 0 || $paymentCredential->stripe_status=='active' ||  $paymentCredential->razorpay_status=='active'))
                                    <td>
                                        @php
                                            $is_plan = Illuminate\Support\Str::contains(json_encode(company()->subscriptions), $package->stripe_annual_plan_id)
                                        @endphp
                                        @if ((!is_null($package->stripe_annual_plan_id) || !is_null($package->razorpay_annual_plan_id)))
{{--                                            <button class="btn btn-success buy-plan"  data-package-id="{{ $package->id }}" data-package-type="annual">@lang('app.buy') @lang('app.plan')</button>--}}
                                            <button class="btn {{$is_plan ? 'btn-danger' : 'btn-success buy-plan'}}" {{$is_plan ? '' : 'data-toggle="modal" data-target="#myModal"'}} data-package-id="{{ $package->id }}" data-package-type="annual" data-stripe-id="{{ $package->stripe_monthly_plan_id }}">{{$is_plan ? __('app.currentPlan') : __('app.buy')}}</button>

                                        @endif
                                    </td>
                                    @endif
                              @endforeach
                           </tr>
                        </tbody>
                     </table>
                  </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-js')

<script src="https://js.stripe.com/v3/"></script>
{{--<script src="https://checkout.razorpay.com/v1/checkout.js"></script>--}}

<script>

    $('body').on('click', '.buy-plan', function () {
        var id = $(this).data('package-id');
        var type = $(this).data('package-type');
        var url = "{{ route('admin.billing.select-package',':id') }}?type=" + type;
        url = url.replace(':id', id);
        console.log(url);
        $(modal_lg + ' ' + modal_heading).html('...');
        $.ajaxModal(modal_lg, url);
    });

   $(document).ready(function() {
      $('#country-name').select2({
         placeholder: "Choose Country *",
         allowClear: false
      });
   });

   $('body').on('click', '.payment-methods', function () {
        $('.payment-methods').removeClass('payment-method-active');
        $(this).addClass('payment-method-active');
   });

   $("#myModal").on('hidden.bs.modal', function() {
      $('.payment-methods').removeClass('payment-method-active');
   });

</script>

@endpush
