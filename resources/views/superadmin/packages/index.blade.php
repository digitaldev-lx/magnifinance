@extends('layouts.master')

@push('head-css')
    <style>
        .private-package {
            color: #ea4c89;
        }
        .recommended-package {
            font-weight: lighter;
            background-color: #d2f1ec;
            color: #25d6b9;
        }
    </style>
@endpush



@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="info-box link-stats">
                <span class="info-box-icon bg-info"><i class="fa fa-dropbox"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('app.total') @lang('app.package')</span>
                    <span class="info-box-number" id="completed-booking">{{ $totalPackages }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="info-box link-stats">
                <span class="info-box-icon bg-success"><i class="fa fa-dropbox"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('app.active') @lang('app.package')</span>
                    <span class="info-box-number" id="completed-booking">{{ $activePackages }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="info-box link-stats">
                <span class="info-box-icon bg-danger"><i class="fa fa-dropbox"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('app.inactive') @lang('app.package')</span>
                    <span class="info-box-number" id="completed-booking">{{ $deActivePackages }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-center justify-content-md-end mb-3">
                        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'))
                            <a href="{{ route('superadmin.settings.index') }}#free-trial" class="btn btn-rounded btn-info mb-1 mr-2" id="settingTrialPage"><i class="fa fa-angle-double-right"></i> @lang('app.freeTrialSettings')</a>
                        @endif
                        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('create_package'))
                            <a href="{{ route('superadmin.packages.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                        @endif
                    </div>
                    <div class="table-responsive">
                    <table id="myTable" class="table w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.name')</th>
                                <th>@lang('app.maxEmployees')</th>
                                <th>@lang('app.maxServices')</th>
                                <th>@lang('app.maxDeals')</th>
                                <th>@lang('app.maxRoles')</th>
                                <th>@lang('app.packageModules')</th>
                                <th>@lang('app.status')</th>
                                <th class="text-right">@lang('app.action')</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="alert alert-info">
                <p>@lang('app.note') : </p>
                <ul>
                    <li>@lang('app.defaultPackageNote')</li>
                    <li>@lang('app.trailPackageNote')</li>
                    <li>@lang('app.packagePaymentFailNote')</li>
                </ul>
            </div>
        </div>
    </div>

@endsection

@push('footer-js')
    <script>
        $(document).ready(function() {
            var table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('superadmin.packages.index') !!}',
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
                    { data: 'DT_RowIndex'},
                    { data: 'name', name: 'name' },
                    { data: 'max_employees', name: 'max_employees' },
                    { data: 'max_services', name: 'max_services' },
                    { data: 'max_deals', name: 'max_deals' },
                    { data: 'max_roles', name: 'max_roles' },
                    { data: 'package_modules', name: 'package_modules' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '11%' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('click', '.delete-row', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('superadmin.packages.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
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
        });
    </script>
@endpush
