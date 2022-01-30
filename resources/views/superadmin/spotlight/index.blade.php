@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-center justify-content-md-end mb-3">
                    <a href="{{ route('superadmin.spotlight-deal.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                    </div>
                    <div class="table-responsive">
                    <table id="spotlight" class="table w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('menu.companies') @lang('app.name')</th>
                                <th>@lang('app.deal') @lang('app.name')</th>
                                <th>@lang('report.fromDate')</th>
                                <th>@lang('report.toDate')</th>
                                <th>@lang('app.sequence')</th>
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
        var spotlight = $('#spotlight').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!! route('superadmin.spotlight-deal.index') !!}',

            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                { data: 'DT_RowIndex'},
                { data: 'company_name', name: 'company_name' },
                { data: 'deal_name', name: 'deal_name' },
                { data: 'from_date', name: 'from_date' },
                { data: 'to_date', name: 'to_date' },
                { data: 'sequence', name: 'sequence' },
                { data: 'action', name: 'action', width: '20%' }
            ]
        });

        $('body').on('click', '.delete-spotlight-row', function(){
            var id = $(this).data('row-id');

            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }).then((willDelete) => {
                if (willDelete) {
                    var token = "{{ csrf_token() }}";

                    var url = "{{ route('superadmin.spotlight-deal.destroy',':id') }}";
                    url = url.replace(':id', id);

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                spotlight._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('change', '.spotlight-sequence', function(){
            var sequence = $(this).val();
            var id = $(this).attr('id');

            var url = "{{ route('superadmin.updateSequence',':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'POST',
                url: url,
                data: {sequence:sequence,'_token':'{{ csrf_token() }}'},
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
                        spotlight._fnDraw();
                    }
                }
            });
        });
    </script>
@endpush
