@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/bootstrap-datepicker.css') }} " rel="stylesheet">
    <link href=" {{ asset('front/css/booking-step-1.css') }} " rel="stylesheet">
    <style>
        #msg_div {
            color: crimson;
        }
    </style>
@endpush

@section('content')
    <!-- BOOKING SECTION START -->
    <section class="booking_step_section">
        <div class="container">
            <div class="row ">
                <div class="col-12 booking_step_heading text-center">
                    <h1>@lang('front.selectBookingDateAndTime')</h1>
                </div>
                <div class="col-12 step_1_booking_date">
                    <form class="mx-auto">
                        <div class="mx-auto" id="datepicker"></div>
                        <input type="hidden" id="booking_date" name="booking_date">
                    </form>
                </div>
                <div class="col-12 slots-wrapper"> </div>
                <div class="col-12">
                    <center>
                        <h5 id="msg_div"></h5>
                    </center>
                </div>

                <div class="col-12">
                    <div class="booking_detail_btn mx-auto">
                        <a id="nextBtn" href="javascript:;" class="btn btn-custom btn-dark add-booking-details">@lang('front.navigation.toCheckout') <i class="zmdi zmdi-long-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- BOOKING SECTION END -->
@endsection

@push('footer-script')
    <script src="{{ asset('front/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            @if (sizeof($bookingDetails) > 0)
                getBookingSlots({ bookingDate:  '{{ $bookingDetails['bookingDate'] }}', _token: "{{ csrf_token() }}"});

                var bookingDate = '{{ $bookingDetails['bookingDate'] }}';

                bookingDetails.bookingDate = bookingDate;
                $('#datepicker').datepicker('update', dateFormat(new Date(bookingDate), 'yyyy-mm-dd', true));
            @endif
        });

        $('#datepicker').datepicker({
            templates: {
                leftArrow: '<i class="fa fa-chevron-left"></i>',
                rightArrow: '<i class="fa fa-chevron-right"></i>'
            },
            startDate: '-0d',
            language: '{{ $locale }}',
            weekStart: 0,
            format: "yyyy-mm-dd"
        });

        var bookingDetails = {_token: $("meta[name='csrf-token']").attr('content')};

        function getBookingSlots(data) {
            $('#msg_div').html('');


            $.easyAjax({
                url: "{{ route('front.bookingSlots') }}",
                type: "POST",
                blockUI: false,
                data: data,
                success: function (response) {
                    console.log(response);
                    if(response.status == 'success') {
                        $('.slots-wrapper').html(response.view);
                        $('#max_booking_per_slot').hide();
                        // check for cookie
                        @if (sizeof($bookingDetails) > 0)
                        $('.slots-wrapper').css('display', 'flex');

                            var bookingTime = '{{ $bookingDetails['bookingTime'] }}';
                            var bookingDate = '{{ $bookingDetails['bookingDate'] }}';
                            var emp_name    = '{{ $bookingDetails['emp_name'] }}';

                            if (bookingDate == bookingDetails.bookingDate) {
                                bookingDetails.bookingTime = bookingTime;
                                $(`input[value='${bookingTime}']`).attr('checked', true);
                                if(emp_name == ''){ emp_name = '@lang("app.noEmployee")';  }
                                $('#show_emp_name_div').removeClass('d-none');
                                $('#show_emp_name_div').html(emp_name+' @lang("front.isSelectedForBooking")..!');
                            } else {
                                bookingDetails.bookingTime = '';
                            }
                        @else
                        bookingDetails.bookingTime = '';
                        @endif
                    } else {
                        $('.slots-wrapper').html('');
                        $('.slots-wrapper').css('display', 'none');
                        $('#msg_div').html(response.msg);
                    }
                    $('#selectedBookingDate').html(data.bookingDate);
                },
                error: function (error){
                    console.log(error);
                }
            })
        }

        $('#datepicker').on('changeDate', function() {
          $('.slots-wrapper').css({'display': 'flex', 'align-items': 'center'});
          var initialHeight = $('.slots-wrapper').css('height');
          var html = '<div class="loading text-white d-flex align-items-center" style="height: '+initialHeight+';">Loading... </div>';
          $('.slots-wrapper').html(html);

          $('html, body').animate({
              scrollTop: $(".slots-wrapper").offset().top
          }, 1000);

          var formattedDate = $('#datepicker').datepicker('getFormattedDate');
          $('#booking_date').val(formattedDate);
          bookingDetails.bookingDate = formattedDate;

          getBookingSlots({ bookingDate:  bookingDetails.bookingDate, _token: "{{ csrf_token() }}"})
        });

        $(document).on('change', $('input[name="booking_time"]'), function (e) {
            bookingDetails.bookingTime = $(this).find('input[name="booking_time"]:checked').val();
        });

        $('body').on('click', '.add-booking-details', function() {
            bookingDetails.selected_user = $('#selected_user').val();

            $.easyAjax({
                url: '{{ route('front.addBookingDetails') }}',
                type: 'POST',
                blockUI: false,
                data: bookingDetails,
                disableButton: true,
                buttonSelector: "#nextBtn",
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = '{{ route('front.checkoutPage') }}'
                    }
                }, error: function (err) {
                    var errors = err.responseJSON.errors;
                    for (var error in errors) {
                        $.showToastr(errors[error][0], 'error')
                    }
                }
            });
        });


        $('body').on('click', '.check-user-availability', function() {
            let date = $(this).data('date');
	        let radioId = $(this).data('radio-id');
	        let time = $(this).data('time');

            $('#select_user_div').addClass('d-none');
            $('#no_emp_avl_msg').addClass('d-none');
            $('#show_emp_name_div').addClass('d-none');

            $.easyAjax({
                url: '{{ route('front.checkUserAvailability') }}',
                type: 'POST',
                blockUI: false,
                container: 'section.section',
                data: {date:date, _token: "{{ csrf_token() }}" },
                success: function (response) {
                    if(response.status === 'fail'){
                        $('#max_booking_per_slot').show();
                    }
                    else{
                        $('#max_booking_per_slot').hide();

                        if(typeof response.select_user !== 'undefined'){
                            $('#select_user_div').removeClass('d-none');
                            $('#select_user').html(response.select_user);
                        }
                    }
                    if (response.continue_booking == 'no') {
                        $('#no_emp_avl_msg').removeClass('d-none');
                        $('#timeSpan').html(time);
                        $('#radio'+radioID).prop("checked", false);
                    } else{
                        $('#no_emp_avl_msg').addClass('d-none');
                        if(typeof response.select_user !== 'undefined'){
                            $('#select_user_div').removeClass('d-none');
                            $('#select_user').html(response.select_user);
                        }
                    }
                }
            });
        });
  </script>
@endpush
