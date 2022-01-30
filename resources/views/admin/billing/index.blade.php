@extends('layouts.master')

@push('head-css')
    <style>
        .alert-warning {
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
        .card-header a {
            color: #fff;
        }
        #changePlan {
            text-decoration: none;
        }
        #package-details {
            width: 60%;
            margin: 0 auto;
            font-size: 15px;
            font-weight: 400;
        }
        #myTable_wrapper {
            width: 100%;
        }
        .d-none {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @php \Illuminate\Support\Facades\Session::forget('success');@endphp
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                    @php \Illuminate\Support\Facades\Session::forget('error');@endphp
            @endif
                @if($message)
                    <div class="alert alert-success">{{ $message }}</div>
                @endif
            <div class="card card-light">
                <div class="card-header" >
                    <div class="alert alertwarning">
                        @lang('app.yourCurrentPlan') <strong class="text-uppercase">({{ $package->name }})</strong>
                        @if(!is_null($firstInvoice) && $stripeSettings->api_key != null && $stripeSettings->api_secret != null && $firstInvoice->method == 'Stripe')
                            @if(!is_null($subscription) && $subscription->ends_at == null)
                                <button type="button" class="btn btn-danger waves-effect waves-light unsubscription" data-type="stripe" title="Unsubscribe Plan"><i class="fa fa-ban display-small"></i> <span class="display-big">@lang('modules.billing.unsubscribe')</span></button>
                            @endif
                        @elseif(!is_null($firstInvoice) && $stripeSettings->paypal_client_id != null && $stripeSettings->paypal_secret != null && $firstInvoice->method == 'Paypal')
                            @if(!is_null($paypalInvoice) && $paypalInvoice->end_on == null  && $paypalInvoice->status == 'paid')
                                <button type="button" class="btn btn-danger waves-effect waves-light unsubscription" data-type="paypal" title="Unsubscribe Plan"><i class="fa fa-ban display-small"></i> <span class="display-big">@lang('modules.billing.unsubscribe')</span></button>
                            @endif
                            @elseif(!is_null($firstInvoice) && $stripeSettings->razorpay_key != null && $stripeSettings->razorpay_secret != null && $firstInvoice->method == 'Razorpay')
                            @if(!is_null($razorPaySubscription) && $razorPaySubscription->ends_at == null)
                                <button type="button" class="btn btn-danger waves-effect waves-light unsubscription" data-type="razorpay" title="Unsubscribe Plan"><i class="fa fa-ban display-small"></i> <span class="display-big">@lang('modules.billing.unsubscribe')</span></button>
                            @endif

                        @else

                        @endif
                        <div class="pull-right change_plan mb-2">
                            <a href=" {{ route('admin.billing.change-plan') }}" class="btn btn-block btn-success waves-effect text-center text-white" id="changePlan">@lang('app.changePlan')</a>
                         </div>
                    </div>
                </div>

                <div class="card-body">
                    <div id="package-details">
                        <div class="row f-15 mt-3 mb-4">
                            <div class="col-md-9">@lang('modules.package.maxEmployee')</div>
                            <div class="col-md-3">{{ $package->max_employees }}</div>
                        </div>
                        <div class="row f-15 mt-3 mb-4">
                            <div class="col-md-9 ">@lang('modules.package.maxService')</div>
                            <div class="col-md-3">{{ $package->max_services }}</div>
                        </div>
                        <div class="row f-15 mt-3 mb-4">
                            <div class="col-md-9 ">@lang('modules.package.maxDeals')</div>
                            <div class="col-md-3">{{ $package->max_deals }}</div>
                        </div>
                        <div class="row f-15 mt-3 mb-4">
                            <div class="col-md-9 ">@lang('modules.package.maxRoles')</div>
                            <div class="col-md-3">{{ $package->max_roles }}</div>
                        </div>
                        @if ($package->type == 'trial')
                            <div class="row f-15 mt-3 mb-4">
                                <div class="col-md-9 ">@lang('modules.package.validForDays')</div>
                                <div class="col-md-3">{{ $package->no_of_days }}</div>
                            </div>
                        @endif


                        @if ($package->type != ('trial' || 'demo') )
                            <div class="row f-15 mt-3 mb-4">
                                <div class="col-md-9 ">@lang('modules.package.monthlyPrice')</div>
                                <div class="col-md-3">{{ $package->monthly_price }}</div>
                            </div>
                            <div class="row f-15 mt-3 mb-4">
                                <div class="col-md-9 ">@lang('modules.package.annualPrice')</div>
                                <div class="col-md-3">{{ $package->annual_price }}</div>
                            </div>
                        @endif

                        <div class="row f-15 mt-3 mb-4">
                            <div class="col-md-9 ">@lang('modules.package.nextPaymentDate')</div>
                            <div class="col-md-3"> @if($nextPaymentDate) {{ $nextPaymentDate }} @else {{ $nextPaymentDate }} @endif</div>
                        </div>
                        <div class="row f-15 mt-3 mb-4">
                            <div class="col-md-9 ">@lang('modules.package.previousPaymentDate')</div>
                            <div class="col-md-3">  @if($previousPaymentDate) {{ $previousPaymentDate }} @else {{ $previousPaymentDate }} @endif </div>
                        </div>
                    </div>

                    <div class="row col-sm-12 mt-5 mb-3">
                        <h5>@lang('modules.package.invoices')</h5>
                    </div>

                    <div class="row col-sm-12 mt-3">
                        <table class="table color-table inverse-table dataTable no-footer dtr-inline" id="myTable" role="grid">
                           <thead>
                              <tr role="row">
                                 <th class="sorting_disabled">#</th>
                                 <th>@lang('modules.package.Packages')</th>
                                 <th>@lang('modules.payments.amount') </th>
                                 <th>@lang('modules.package.date')</th>
                                 <th>@lang('modules.package.nextPaymentDate')</th>
                                 <th>@lang('modules.payments.paymentGateway')</th>
                                 <th>@lang('modules.payments.action')</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr class="odd">
                                 <td valign="top" colspan="7" class="dataTables_empty">@lang('modules.payments.noDataAvailable')</td>
                              </tr>
                           </tbody>
                        </table>
                        <div id="users-table_processing" class="dataTables_processing panel panel-default d-none">@lang('modules.datatables.processing')...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-js')
<script src="{{ asset('js/swal/sweet-alert.min.js') }}"></script>
<script>
    $(document).ready(function() {

        var table = $('#myTable').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.billing.data') }}",
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
                $('.role_id').select2({
                    width: '100%'
                });
            },
            columns: [
                { data: 'id', name: 'id' ,bSort: false },
                { data: 'name', name: 'name' },
                { data: 'amount', name: 'amount' },
                { data: 'paid_on', name: 'paid_on' },
                { data: 'next_pay_date', name: 'next_pay_date' },
                { data: 'method', name: 'method' },
                { data: 'action', name: 'action' }
            ]
        });
        new $.fn.dataTable.FixedHeader( table );

    });

    $('body').on('click', '.unsubscription', function(){
        var type = $(this).data('type');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.unsubscribe')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.confirmUnsubscribe')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }).then(function (isConfirmed) {
            if (isConfirmed) {
                var url = "{{ route('admin.billing.unsubscribe') }}";
                var token = "{{ csrf_token() }}";
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'POST', 'type': type},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });
</script>
@endpush
