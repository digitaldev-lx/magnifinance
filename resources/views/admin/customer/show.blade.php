@extends('layouts.master')

@push('head-css')
    <style>
        #myTable td{
            padding: 0;
        }
        .status{
            font-size: 80%;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-widget widget-user-2">
                <div class="widget-user-header">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" src="{{ $customer->user_image_url }}" height="60em" width="60em" alt="User Avatar">
                            </div>
                        </div>
                        <div class="col-md-10 text-white">
                            <h3 class="widget-user-username">{{ ucwords($customer->name) }}
                                @permission('update_customer')
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-outline-light">@lang('app.edit')</a>
                                @endpermission
                                @permission('delete_customer')
                                <a href="javascript:;" class="btn btn-outline-light delete-row" data-row-id="{{ $customer->id }}">@lang('app.delete')</a>
                                @endpermission
                            </h3>
                            <div>
                                <p><i class="fa fa-envelope"></i>: {{ $customer->email }}</p>
                                <p><i class="fa fa-phone"></i>: {{ $customer->mobile ? $customer->formatted_mobile : '--' }}</p>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-footer row">

                    <div class="col-md-10 offset-md-2">
                        <div class="row">

                            <div class="col-md-12">
                                <h4>@lang('modules.customer.bookingStats')</h4>
                            </div>
                            <div class="col-md-12">
                                <div class="row" id="customer-stats">
                                    @include('partials.customer_stats')
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="" id="filter-status" class="form-control">
                                    <option value="">@lang('app.filter') @lang('app.status'): @lang('app.viewAll')</option>
                                    <option value="completed">@lang('app.completed')</option>
                                    <option value="pending">@lang('app.pending')</option>
                                    <option value="in progress">@lang('app.in progress')</option>
                                    <option value="canceled">@lang('app.canceled')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control datepicker" name="filter_date" id="filter-date" placeholder="@lang('app.booking') @lang('app.date')">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <button type="button" id="reset-filter" class="btn btn-danger"><i class="fa fa-times"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive">


                                <table id="myTable" class="table table-borderless w-100">
                                    <thead class="hide">
                                    <tr>
                                        <th>#</th>
                                    </tr>
                                    </thead>
                                </table>

                            </div>

                        </div>

                        <div class="col-md-5 offset-md-1" id="booking-detail">

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-js')
    <script>
        $(document).ready(function() {

            $('.select2').select2();

            $('.datepicker').datetimepicker({
                format: '{{ $date_picker_format }}',
                locale: '{{ $settings->locale }}',
                allowInputToggle: true,
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: "fa fa-angle-double-left",
                    next: "fa fa-angle-double-right",
                }
            }).on("dp.change", function (e) {
                table._fnDraw();
            });

            function updateBooking(currEle) {
                let url = '{{route('admin.bookings.update', ':id')}}';
                url = url.replace(':id', currEle.data('booking-id'));

                $.easyAjax({
                    url: url,
                    container: '#update-form',
                    type: "POST",
                    data: $('#update-form').serialize(),
                    success: function (response) {
                        if (response.status == "success") {
                            $('#booking-detail').hide().html(response.view).fadeIn('slow');
                            $('#customer-stats').hide().html(response.customerStatsView).fadeIn('slow');
                            table._fnDraw();
                        }
                    }
                })
            }

            $('body').on('click', '#update-booking', function () {
                let cartItems = $("input[name='item_prices[]']").length;
                let product_cartItems=$("input[name='product_prices[]']").length;

                if(cartItems === 0 && product_cartItems === 0){
                    swal('@lang("modules.booking.addItemsToCart")');
                    $('#cart-item-error').html('@lang("modules.booking.addItemsToCart")');
                    return false;
                }
                else {
                    $('#cart-item-error').html('');
                    var updateButtonEl = $(this);
                    if ($('#booking-status').val() == 'completed' && $('#payment-status').val() == 'pending' && $('.fa.fa-money').parent().text().indexOf('cash') !== -1) {
                        swal({
                            text: '@lang("modules.booking.changePaymentStatus")',
                            closeOnClickOutside: false,
                            buttons: [
                                'NO', 'YES'
                            ]
                        }).then(function (isConfirmed) {
                            if (isConfirmed) {
                                $('#payment-status').val('completed');
                            }
                            updateBooking(updateButtonEl);
                        });
                    }
                    else {
                        updateBooking(updateButtonEl);
                    }
                }

            });

            var table = $('#myTable').dataTable({
                responsive: true,
                "searching": false,
                serverSide: true,
                "ordering": false,
                ajax: {'url' : '{!! route('admin.bookings.index') !!}',
                    "data": function ( d ) {
                        return $.extend( {}, d, {
                            "filter_status": $('#filter-status').val(),
                            "filter_customer": '{{ $customer->id }}',
                            "filter_date": $('#filter-date').val(),
                        } );
                    }
                },
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'id', name: 'id' }
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
                            var url = "{{ route('admin.bookings.destroy',':id') }}";
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
                                        $('#booking-detail').html('');
                                    }
                                }
                            });
                        }
                    });
            });

            $('body').on('click', '.cancel-row', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.bookings.requestCancel',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'POST'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    table._fnDraw();
                                    $('#booking-detail').html('');
                                }
                            }
                        });
                    }
                });
            });

            $('#myTable').on('click', '.view-booking-detail', function () {
                let bookingId = $(this).data('booking-id');
                let current_url = "?current_url="+'customerPage';
                let url = "{{ route('admin.bookings.show', ':id') }}"+current_url;
                url = url.replace(':id', bookingId);

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                        if (response.status == "success") {
                            $('html, body').animate({
                                scrollTop: $("#booking-detail").offset().top-50
                            }, 2000);
                            $('#booking-detail').hide().html(response.view).fadeIn('slow');
                        }
                    }
                });
            });

            $('body').on('click', '.edit-booking', function () {
                let bookingId = $(this).data('booking-id');
                let current_url = "?current_url="+'customerPage';
                let url = "{{ route('admin.bookings.edit', ':id') }}"+current_url;
                url = url.replace(':id', bookingId);
                
                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                        if (response.status == "success") {
                            $('#booking-detail').hide().html(response.view).fadeIn('slow');
                        }
                    }
                });
            });

            $('#filter-status, #filter-customer').change(function () {
                table._fnDraw();
            })

            $('body').on('click', '#reset-filter', function () {
                $('#filter-status, #filter-date').val('');
                $("#filter-customer").val('').trigger('change');
                table._fnDraw();
            })

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
                        var url = "{{ route('admin.customers.destroy',':id') }}";
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
                                    $('#booking-detail').html('');
                                }
                            }
                        });
                    }
                });
            });

            $('body').on('click', '.send-reminder', function () {
                let bookingId = $(this).data('booking-id');
                $.easyAjax({
                    type: 'POST',
                    url: '{{ route("admin.bookings.sendReminder") }}',
                    data: {bookingId: bookingId, _token: '{{ csrf_token() }}'}
                });
            });

        });
    </script>
@endpush
