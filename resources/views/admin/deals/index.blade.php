@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('create_deal'))
                        <div class="d-flex justify-content-center justify-content-md-end mb-3">
                            <a href="{{ route('admin.deals.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.image')</th>
                                <th>@lang('app.title')</th>
                                <th>@lang('app.startOn')</th>
                                <th>@lang('app.expireOn')</th>
                                <th>@lang('app.originalPrice')</th>
                                <th>@lang('app.dealPrice')</th>
                                <th>@lang('app.dealUsage')</th>
                                <th>@lang('app.location')</th>
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
            var table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.deals.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'title', name: 'title' },
                    { data: 'start_date_time', name: 'start_date_time' },
                    { data: 'end_date_time', name: 'end_date_time' },
                    { data: 'original_amount', name: 'original_amount' },
                    { data: 'deal_amount', name: 'deal_amount' },
                    { data: 'usage', name: 'usage' },
                    { data: 'deal_location', name: 'deal_location' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '15%' }
                ],
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('click', '.view-deal', function() {
                var id = $(this).data('row-id');
                var url = "{{ route('admin.deals.show',':id') }}";
                url = url.replace(':id', id);
                $(modal_lg + ' ' + modal_heading).html('...');
                $.ajaxModal(modal_lg, url);
            });

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
                        var url = "{{ route('admin.deals.destroy',':id') }}";
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

            $('body').on('click', '.duplicate-row', function () {
                var id = $(this).data('row-id');

                var url = "{{ route('admin.deals.create').'?deal_id=:id' }}";
                url = url.replace(':id', id);
                location.href = url;
            });
        } );
    </script>
@endpush
