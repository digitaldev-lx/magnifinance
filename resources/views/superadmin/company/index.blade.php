@extends('layouts.master')

@section('content')

    <style>
        .label-custom {
            background-color: #01c0c8;
        }
        #modal-header {
            background: #71d2f0;
            width:100%;
        }
        .package-update-button {
            background-color: #01c0c8;padding: 4px 12px 4px;border-radius: 60px;font-size: 75%;color: #fff;
        }
    </style>

    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="info-box link-stats">
                <span class="info-box-icon bg-info"><i class="fa fa-home"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('app.totalCompanies')</span>
                    <span class="info-box-number" id="completed-booking">@if(!is_null($totalCompanies)) {{ $totalCompanies->count('id') }} @else 0 @endif</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="info-box link-stats">
                <span class="info-box-icon bg-success"><i class="fa fa-home"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('app.activeCompanies')</span>
                    <span class="info-box-number" id="completed-booking">{{ $activeCompanies }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="info-box link-stats">
                <span class="info-box-icon bg-danger"><i class="fa fa-home"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('app.inActiveCompanies')</span>
                    <span class="info-box-number" id="completed-booking">{{ $deActiveCompanies }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @permission('create_company')
                        <div class="d-flex justify-content-center justify-content-md-end mb-3">
                            <a href="{{ route('superadmin.companies.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                        </div>
                    @endpermission
                    <div class="text-right"></div>
                    <div class="table-responsive">
                    <table id="myTable" class="table w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.name')</th>
                                <th>@lang('app.email')</th>
                                <th>@lang('app.logo')</th>
                                <th>@lang('app.package')</th>
                                <th>@lang('app.status')</th>
                                <th class="text-right">@lang('app.action')</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer-js')
    <script>
        // Show manage package modal
        $('body').on('click', '.package-update-button', function() {
            var id = $(this).data('company-id');
            var url = "{{ route('superadmin.companies.show',':id') }}";
            url = url.replace(':id', id);
            $(modal_lg + ' ' + modal_heading).html('...');
            $.ajaxModal(modal_lg, url);
        });

        $('body').on('click', '.view-company', function() {
            var id = $(this).data('company-id');
            var url = "{{ route('superadmin.viewCompanyDetail',':id') }}";
            url = url.replace(':id', id);
            $(modal_lg + ' ' + modal_heading).html('...');
            $.ajaxModal(modal_lg, url);
        });

        // Login to vendor account
        $('body').on('click', '#login-to-vendor', function () {
            var id = $(this).data('company-id');
            var url = "{{ route('superadmin.loginAsVendor',':id') }}";
            url = url.replace(':id', id);

            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.login')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.loginAsVendor')",
            }).then((willDelete) => {
                if (willDelete) {
                    $.easyAjax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status == 'success') {
                                location.href = "{{ route('admin.dashboard') }}"
                            }
                        }
                    });
                }
            });

            /* swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.loginInfo')",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#28A745",
                confirmButtonText: "@lang('app.login')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {
                    $.easyAjax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status == 'success') {
                                location.href = "{{ route('admin.dashboard') }}"
                            }
                        }
                    });
                }
            }); */
        })

        $(document).ready(function() {
            var table = $('#myTable').dataTable({
                responsive: true,
                serverSide: true,
                ajax: '{!! route('superadmin.companies.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'logo', name: 'logo' },
                    { data: 'package', name: 'package' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '20%' }
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
                        var url = "{{ route('superadmin.companies.destroy',':id') }}";
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
