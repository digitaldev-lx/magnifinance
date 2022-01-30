@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                            <tr>
                                <th>@lang('app.id')</th>
                                <th>@lang('app.company')</th>
                                <th>@lang('app.package')</th>
                                <th>@lang('app.paymentBy')</th>
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
    <script src="{{ asset('js/swal/sweet-alert.min.js') }}"></script>
    <script>
        var table;
        $(document).ready(function() {
            table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                stateSave: true,
                destroy: true,
                ajax: '{!! route('superadmin.offline-plan.data') !!}',
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
                    {data: 'id', name: 'id'},
                    {data: 'company.company_name', name: 'company.company_name'},
                    {data: 'package.name', name: 'package.name'},
                    {data: 'offline_method.name', name: 'offline_method.name'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'}
                ]
            });
            new $.fn.dataTable.FixedHeader( table );
        });

        $(function () {
            $('body').on('click', '.sa-params', function () {
                var id = $(this).data('user-id');
                swal({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('messages.confirmation.recoverPackage')",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "@lang('messages.deleteConfirmation')",
                    cancelButtonText: "@lang('messages.confirmNoArchive')",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }).then(function (isConfirmed) {
                    if (isConfirmed) {

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
                                    var total = $('#totalPackages').text();
                                    $('#totalPackages').text(parseInt(total) - parseInt(1));
                                    table._fnDraw();
                                }
                            }
                        });
                    }
                });
            });
        });

        $('body').on('click', '.accept-offline-plan-change', function () {
            let id = $(this).data('id');

            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.recoverVerifiedRequest')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.verify')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }).then(function (isConfirmed) {
                if (isConfirmed) {
                    $.easyAjax({
                        type: 'POST',
                        url: '{{ route('superadmin.offline-plan.verify') }}',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.reject-offline-plan-change', function () {
            let id = $(this).data('id');

            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.recoveRejectedRequest')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.reject')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }).then(function (isConfirmed) {
                if (isConfirmed) {
                    $.easyAjax({
                        type: 'POST',
                        url: '{{ route('superadmin.offline-plan.reject') }}',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
    </script>

@endpush
