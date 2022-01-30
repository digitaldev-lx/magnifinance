@extends('layouts.master')

@section('content')

    <div class="row">
        <!-- Tabs  -->
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="nav-item active">
                    <a class="nav-link active" href="#subcriptionInvoices" data-toggle="tab">@lang('app.SubscriptionInvoices')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#bookingsInvoices" data-toggle="tab">@lang('app.bookingsInvoices')</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 tab-content">
            @include('superadmin/invoices/show')
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
            stateSave: true,
            destroy: true,
            ajax: '{!! route('superadmin.invoices.data') !!}',
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
                { data: 'id', name: 'id' },
                { data: 'company', name: 'company'},
                { data: 'package', name: 'package' },
                { data: 'transaction_id', name: 'transaction_id'},
                { data: 'amount', name: 'amount' },
                { data: 'paid_on', name: 'paid_on' },
                { data: 'next_pay_date', name: 'next_pay_date' },
                { data: 'method', name: 'method' },
                { data: 'action', name: 'action' }
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
            })
            .then((willDelete) => {
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

    // Fetch Booking Invoices (Not transferred to admin)
    $(document).ready(function() {
        var table = $('#bookingInvoicesTable').dataTable({
            responsive: true,
            serverSide: true,
            ajax: '{!! route('superadmin.bookingInvoice') !!}',
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                { data: 'DT_RowIndex'},
                { data: 'company', name: 'company' },
                { data: 'transactionId', name: 'transactionId' },
                { data: 'amount', name: 'amount' },
                { data: 'application_fee', name: 'application_fee' },
                { data: 'method', name: 'method' },
                { data: 'paid_on', name: 'paid_on' },
            ]
        });
        new $.fn.dataTable.FixedHeader( table );
    });
</script>
@endpush
