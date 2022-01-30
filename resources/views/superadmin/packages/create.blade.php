@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">

    <style>
        .collapse.in {
            display: block;
        }
        .switch-div {
            display: flex;
        }
        .switch-label {
            padding-left: 10px;
        }
        .checkbox-label {
            padding: 2px;
        }
        #make_private_span {
            background-color: transparent;
            border-left: 0px
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.add') @lang('app.package')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('modules.package.name') </label>
                                <input type="text" class="form-control" name="name" value="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.maxEmployees') </label>
                                <input type="number" class="form-control" name="max_employees" value=""
                                autocomplete="off" onkeypress="return isNumberKey(event)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.maxDeals')</label>
                                <input onkeypress="return isNumberKey(event)" type="number" class="form-control"
                                name="max_deals" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.maxServices')</label>
                                <input onkeypress="return isNumberKey(event)" type="number" class="form-control"
                                name="max_services" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.maxRoles')</label>
                                <input onkeypress="return isNumberKey(event)" type="number" class="form-control"
                                name="max_roles" min="0">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <br>
                            <h5 class="text-uppercase">@lang('modules.package.paymentGatewayPlans')</h5>
                            <br>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('modules.package.monthlyPrice')</label>
                                <input onkeypress="return isNumberKey(event)" type="number" class="form-control"
                                name="monthly_price" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('modules.package.annualPrice')</label>
                                <input onkeypress="return isNumberKey(event)" type="number" class="form-control"
                                name="annual_price" min="0">
                            </div>
                        </div>
                        @if ($paymentCredentials->stripe_status == 'active')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('modules.package.stripeMonthlyPlanId')</label>
                                <input type="text" class="form-control" name="stripe_monthly_plan_id" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('modules.package.stripeYearlyPlanId')</label>
                                <input type="text" class="form-control" name="stripe_annual_plan_id" min="0">
                            </div>
                        </div>
                        @endif
                        @if ($paymentCredentials->razorpay_status == 'active')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('modules.package.razorpayMonthlyPlanId')</label>
                                <input type="text" class="form-control" name="razorpay_monthly_plan_id" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('modules.package.razorpayYearlyPlanId')</label>
                                <input type="text" class="form-control" name="razorpay_annual_plan_id" min="0">
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4 mt-3 mb-3">
                            <div class="switch-div">
                                <label class="switch">
                                <input name="mark_as_recommended" class="lang_status" type="checkbox" value="true"
                                data-lang-id="2">
                                <span class="slider round"></span>
                                </label>
                                <label class="switch-label">@lang('modules.package.markAsRecommended') ? </label>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3 mb-3">
                            <div class="switch-div">
                                <label class="switch">
                                <input name="status" class="lang_status" type="checkbox" value="active"
                                data-lang-id="2">
                                <span class="slider round"></span>
                                </label>
                                <label class="switch-label">@lang('app.status')</label>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3 mb-3">
                            <div class="col-md-12">
                                <input type="checkbox" id="make_private" name="make_private" value="true">
                                <label for="make_private"
                                class="checkbox-label">@lang('modules.package.makePrivate')</label>
                                <span class="fa fa-info-circle text-default"
                                id="make_private_span"
                                data-toggle="tooltip"
                                data-placement="top"
                                title="Private package will not be seen by customers but superadmin can assign it to them from superadmin panel."></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <br>
                            <h5 class="text-uppercase">@lang('modules.package.selectModules')</h5>
                            <br>
                        </div>
                        <div class="col-md-12">
                            <input type="checkbox" id="select_all" name="select_all">
                            <label for="select_all" class="checkbox-label">@lang('app.selectAll')</label>
                            <hr>
                        </div>
                        @foreach ($package_modules as $package_module)
                        <div class="col-md-2">
                            <input required type="checkbox" id="checkbox{{ $package_module->id }}"
                                name="package_modules[{{ $package_module->id }}]" class="package_modules"
                                value="{{ $package_module->name }}">
                            <label for="checkbox{{ $package_module->id }}" class="checkbox-label">
                            {{ $package_module->name }} </label>
                        </div>
                        @endforeach
                        @foreach ($errors->all() as $error)
                        <label>{{ $error }}</label>
                        @endforeach
                        <div class="col-md-12">
                            <br><br><br>
                            <div class="form-group">
                                <label for="name">@lang('app.description')</label>
                                <textarea name="description" id="description" cols="30" class="form-control-lg form-control"
                                rows="4"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                class="fa fa-check"></i> @lang('app.save')</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection

@push('footer-js')
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $('body').on('click', '#select_all', function() {
                if($(this).prop("checked")) {
                    $(".package_modules").prop("checked", true);
                } else {
                    $(".package_modules").prop("checked", false);
                }
            });

            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
                $('#description').summernote({
                    dialogsInBody: true,
                    height: 300,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['fontsize', ['fontsize']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ["view", ["fullscreen"]]
                    ]
                });
            })

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
            })

            $('body').on('click', '#save-form', function() {
                $.easyAjax({
                    url: '{{route('superadmin.packages.store')}}',
                    container: '#createForm',
                    type: "POST",
                    redirect: true,
                    data:$('#createForm').serialize(),
                })
            });

            function isNumberKey(evt) {
                var charCode = (evt.which) ? evt.which : evt.keyCode
                if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
                return true;
            }

    </script>
@endpush
