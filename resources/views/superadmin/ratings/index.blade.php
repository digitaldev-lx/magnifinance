@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-center justify-content-md-end mb-3">
                    <a href="{{ route('superadmin.ratings.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                    </div>
                    <div class="table-responsive">
                    <table id="ratingTable" class="table w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.item')</th>
                                <th>@lang('app.company')</th>
                                <th>@lang('menu.ratings')</th>
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
        $(document).ready(function() {
            var table = $('#ratingTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('superadmin.ratings.index') !!}',
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
                    { data: 'item', name: 'item' },
                    { data: 'company', name: 'company' },
                    { data: 'rating', name: 'rating' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '11%' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('click', '.update-rating', function(){
                let id = $(this).data('row-id');
                let url = "{{ route('superadmin.ratings.edit', ':id') }}";
                url = url.replace(':id', id);
                $(modal_lg + ' ' + modal_heading).html('...');
                $.ajaxModal(modal_lg, url);
            });

            // update feedback status
            $(document).on('change', '.feedback_status', function() {
                var status = $(this).val();
                var id = $(this).data('feedback-id');
                var url = "{{ route('superadmin.ratings.changeStatus', ':id') }}";
                url = url.replace(':id', id);
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.updateStatusWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        $.easyAjax({
                            type: "GET",
                            url: url,
                            data: {
                                'status': status,
                                'id': id
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    table._fnDraw();
                                }
                            }
                        });
                    }
                });
            });

            $('body').on('click', '#update-feedback', function() {
                let id = $(this).data('id');
                let url = "{{ route('superadmin.ratings.update', ':id') }}";
                url = url.replace(':id', id);

                $.easyAjax({
                    url: url,
                    container: '#editFeedback',
                    type: "POST",
                    redirect: true,
                    file:true,
                    success: function (response) {
                        if(response.status == 'success'){
                            $.unblockUI();
                            $(modal_lg).modal('hide');
                            table._fnDraw();
                        }
                    }
                })
            });

            $('body').on('click', '.delete-rating', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('superadmin.ratings.destroy',':id') }}";
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
        } );
    </script>
@endpush
