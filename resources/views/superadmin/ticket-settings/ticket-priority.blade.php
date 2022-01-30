<div class="d-flex justify-content-center justify-content-md-end mb-3">
    <a href="javascript:;" class="btn btn-rounded btn-primary mb-1 add-ticket-priority"><i class="fa fa-plus"></i> @lang('app.addNew')</a>
</div>
<div class="table-responsive">
    <table id="ticketPriorityTable" class="table w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('app.name')</th>
                <th class="text-right">@lang('app.action')</th>
            </tr>
        </thead>
    </table>
</div>

@push('footer-js')
<script>
// Start Priority Script
ticketPriorityTable = $('#ticketPriorityTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('superadmin.ticket-priorities.index') !!}',

                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[1, 'ASC']],
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });
            new $.fn.dataTable.FixedHeader(ticketPriorityTable);

            $('body').on('click', '.add-ticket-priority', function () {
                var url = "{{ route('superadmin.ticket-priorities.create') }}";

                $(modal_default + ' ' + modal_heading).html('@lang('app.edit') @lang('app.tax')');
                $.ajaxModal(modal_default, url);
            });

            $('body').on('click', '.edit-ticket-priority', function () {
                var id = $(this).data('row-id');
                var url = "{{ route('superadmin.ticket-priorities.edit', ':id') }}";
                url = url.replace(':id', id);

                $(modal_default + ' ' + modal_heading).html('@lang('app.edit') @lang('app.tax')');
                $.ajaxModal(modal_default, url);
            });

            $('body').on('click', '.delete-ticket-priority', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                })
                .then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('superadmin.ticket-priorities.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    ticketPriorityTable._fnDraw();
                                }
                            }
                        });
                    }
                });
            });
            // End Priority Script
</script>
@endpush

