<style>
    .msg-container{
        width: 80%;
        margin: 0 auto;
    }
    .d-none{
        display: none
    }
    #selectEmployeeMSG {
        font-size: 16px
    }
</style>

<div class="available_appointments mx-auto">
    <div class="col-12 d-flex justify-content-center align-items-center py-4 available_appointments_heading">
        <i class="zmdi zmdi-time"></i>
        <p>@lang('front.availableAppointmentsOn') <span id="selectedBookingDate"></span></p>
    </div>

    @if ($bookingTime->status == 'enabled')
        @if ($bookingTime->multiple_booking === 'yes' && $bookingTime->max_booking != 0 && $bookings->count() >= $bookingTime->max_booking)
            <div class="col-12 alert alert-custom">
                @lang('front.maxBookingLimitReached')
            </div>
        @else
            <div class="col-12 align-items-center available_time">

                @php
                    $slot_count = 1;
                    $check_remaining_booking_slots = 0;
                @endphp
                @for ($d = $startTime; $d < $endTime; $d->addMinutes($bookingTime->slot_duration))
                    @php $slotAvailable = 1; @endphp
                    @if ($bookingTime->multiple_booking === 'no' && $bookings->count() > 0)
                        @foreach ($bookings as $booking)
                            @if ($booking->date_time->format($settings->time_format) == $d->format($settings->time_format))
                                @php $slotAvailable = 0; @endphp
                            @endif
                        @endforeach
                    @endif

                    @if ($slotAvailable == 1)
                        @php $check_remaining_booking_slots++; @endphp

                        <div class="custom-control custom-radio custom-control-inline check-user-availability"
                             data-date="{{ $d }}"
                             data-radio-id="{{ $slot_count }}"
                             {{--                                    data-time="{{ $d->format($settings->time_format) }}">--}}
                             data-time="{{ $d->format($company->time_format) }}">


                            <input type="radio" id="radio{{ $slot_count }}" name="booking_time"
                                   class="custom-control-input mr-3" value="{{ $d->format('H:i:s') }}">
                            <label class="custom-control-label ml-4"
                                   {{--                                        for="radio{{ $slot_count }}">{{ $d->format($settings->time_format) }}</label>--}}
                                   for="radio{{ $slot_count }}">{{ $d->format($company->time_format) }}</label>
                        </div>

                    @endif
                    @php $slot_count++; @endphp
                @endfor


                <div class="col-12 align-items-center msg-container">
                    <div class="row mt-30">
                        <div class="col-12" id="max_booking_per_slot" >
                            <center>
                                <h5 style="color: crimson;" >@lang('messages.reachMaxBookingPerSlot')</h5>
                            </center>
                        </div>
                    </div>

                    <div class="alert-custom text-center text-danger mb-5 d-none" id="select_user_div">
                        <span id="selectEmployeeMSG">@lang('messages.booking.selectEmployeeMSG')</span>
                        <span id="select_user"></span>
                    </div>

                    <div class="alert-custom text-center text-danger mb-5 d-none" id="show_emp_name_div">
                    </div>

                    <div class="alert-custom text-center text-danger mb-5 d-none" id="no_emp_avl_msg">
                        @lang('front.noEmployeeAvailableAt') <span id="timeSpan"><span>.
                    </div>


                    @if ($slot_count == 1 || $check_remaining_booking_slots == 0)
                        <div class="alert-custom text-center text-danger mb-5">
                            @lang('front.bookingSlotNotAvailable')
                        </div>
                    @endif

                </div>

            </div>
        @endif
    @endif
</div>
