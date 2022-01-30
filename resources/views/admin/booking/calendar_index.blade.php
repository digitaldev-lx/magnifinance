@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.min.css') }}">
    <style>
        #booking-detail {
            padding: 3%;
        }
        #coupon-detail-modal {
            margin-left: 3%;
            margin-top: 45%;
        }
        .eventIcon {
            color: rgb(28, 32, 224);
        }
        #modal-close {
            margin-right: 43%;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer-js')
    <script src="{{ asset('js/fullcalendar.min.js') }}"></script>

    @if($credentials->stripe_status == 'active' && !$user->is_admin)
        <script src="https://js.stripe.com/v3/"></script>
    @endif

    @if($credentials->razorpay_status == 'active' && !$user->is_admin)
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    @endif

    @if (Session::has('success'))
        <script>
            toastr.success("{!!  Session::get('success') !!}");
            {{ Session::forget('success') }}
        </script>
    @endif

    <script>
        $('#calendar').fullCalendar({
            header: {
                left   : 'prev,next today',
                center : 'title',
                right  : 'month,agendaWeek,agendaDay',
            },
            events : [
                @foreach($bookings as $booking)
                {
                    title    : '{{ $booking->user ? $booking->user->name : '' }}',
                    start    : '{{ $booking->date_time }}',
                    id       : '{{ $booking->id }}',
                    coupon   : '{{ $booking->coupon_id }}',
                    status   : '{{ $booking->status }}',
                    textColor: 'white',
                },
                @endforeach
            ],
            eventOverlap: true,
            timeFormat  : 'hh:mm a',
            locale      : '{{ $settings->locale }}',
            eventLimit  : true,
            views: {
                month : {
                eventLimit: 5
                }
            },
            eventRender: function(event, eventElement, view) {
                if (event.title) {
                    eventElement.find('.fc-title').prepend('&nbsp&nbsp<i class="fa fa-user-circle eventIcon" aria-hidden="true"></i>&nbsp;');
                }
                eventElement.find('.fc-time').prepend('<i class=" fa fa-clock-o eventIcon" aria-hidden="true"></i>&nbsp');
            },
            eventAfterRender: function (event, element, view) {

                if (event.status == 'completed') {
                    element.css('background-color', '#28a745');
                } else if (event.status == 'canceled') {
                    element.css('background-color', '#ee4444');
                } else if (event.status == 'pending') {
                    element.css('color', '#000000');
                    element.css('background-color', '#ffc107');
                } else if (event.status == 'approved') {
                    element.css('background-color', '#17a2b8');
                } else if (event.status == 'in progress') {
                    element.css('background-color', '#007bff');
                }
            },
            eventClick: function(calEvent, jsEvent, view) {
                let id = (calEvent.id);
                let current_url = "?current_url="+'calendarPage';
                let url = "{{ route('admin.bookings.show', ':id') }}"+current_url;
                url = url.replace(':id', id);

                $(modal_lg + ' ' + modal_heading).html('...');
                $.ajaxModal(modal_lg, url);
            },
            editable : true,
            eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
                let couponId = (event.coupon);

                let id = (event.id);
                let url = "{{ route('admin.bookings.update_booking_date', ':id') }}";
                url = url.replace(':id', id);

                let newDate = (event.start);
                startDate =  moment(event.start).format('Y-MM-DD HH:mm:ss');

                $.easyAjax({
                    url : url,
                    type: "POST",
                    data: {
                        id: id,
                        startDate: startDate,
                        couponId : couponId,
                        '_method':'PUT',
                        '_token' : '{{ csrf_token() }}'
                    },
                    success: function (response, event) {
                        $('#calendar').fullCalendar('refetchEvent');
                    },
                    error: function (xhr, status, error) {
                        console.log("fail");
                    }
                });
            },
        });

        $(document).ready(function()
        {
            $('body').on('click', '.delete-row', function(){
                var id = $(this).data('row-id');
                swal({
                    icon    : "warning",
                    buttons : ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title   : "@lang('errors.areYouSure')",
                    text    : "@lang('errors.deleteWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.bookings.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url : url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    setTimeout(function () {
                                        window.location.reload(1);
                                    }, 2000);
                                }
                            }
                        });
                    }
                });
            });

            $('body').on('click', '.cancel-row', function(){
                var id = $(this).data('row-id');
                swal({
                    icon    : "warning",
                    buttons : ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title   : "@lang('errors.areYouSure')",
                })
                .then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.bookings.requestCancel',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url : url,
                            data: {'_token': token, '_method': 'POST'},
                            success: function (response) {
                                if (response.status == "success") {
                                    location.reload();
                                }
                            }
                        });
                    }
                });
            });

            $('body').on('click', '.edit-booking', function () {
                let bookingId = $(this).data('booking-id');
                let url = '{{ route('admin.bookings.edit', ':id') }}';
                url = url.replace(':id', bookingId);
                $(modal_lg).hide()
                $(modal_lg + ' ' + modal_heading).html('...');
                $.ajaxModal(modal_lg, url);
            });

            function updateBooking(currEle) {
                let url = '{{route('admin.bookings.update', ':id')}}';
                url = url.replace(':id', currEle.data('booking-id'));
                $.easyAjax({
                    url : url,
                    container: '#update-form',
                    type: "POST",
                    data: $('#update-form').serialize(),
                    success: function (response) {
                        if (response.status == "success") {
                            let current_url = "?current_url="+'calendarPage';
                            let url = "{{ route('admin.bookings.show', ':id') }}"+current_url;
                            url = url.replace(':id', currEle.data('booking-id'));
                            $(modal_lg).hide()
                            $(modal_lg + ' ' + modal_heading).html('...');
                            $.ajaxModal(modal_lg, url);
                        }
                    }
                })
            }

            $('body').on('click', '#update-booking', function () {
                let cartItems = $("input[name='item_prices[]']").length;
                let product_cartItems = $("input[name='product_prices[]']").length;

                if(cartItems === 0 && product_cartItems ===0){
                    swal('@lang("modules.booking.addItemsToCart")');
                    $('#cart-item-error').html('@lang("modules.booking.addItemsToCart")');
                    return false;
                }
                else {
                    $('#cart-item-error').html('');
                    var updateButtonEl = $(this);
                    if ($('#booking-status').val() == 'completed' && $('#payment-status').val() == 'pending' && $('.fa.fa-money').parent().text().indexOf('cash') !== -1) {
                        swal({
                            text    : '@lang("modules.booking.changePaymentStatus")',
                            closeOnClickOutside: false,
                            buttons : [
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

            $('body').on('click', '.send-reminder', function () {
                let bookingId = $(this).data('booking-id');

                $.easyAjax({
                    type: 'POST',
                    url : '{{ route("admin.bookings.sendReminder") }}',
                    data: {bookingId: bookingId, _token: '{{ csrf_token() }}'}
                });
            });

        });
    </script>
@endpush
