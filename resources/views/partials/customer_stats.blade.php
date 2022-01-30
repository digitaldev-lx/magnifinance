<div class="col-md-2 text-center mt-3 border-right">
    <h6 class="text-uppercase">@lang('app.completed')</h6>
    <p>{{ $completedBookings }}</p>
</div>

<div class="col-md-2 text-center mt-3 border-right">
    <h6 class="text-uppercase">@lang('app.approved')</h6>
    <p>{{ $approvedBookings }}</p>
</div>

<div class="col-md-2 text-center mt-3 border-right">
    <h6 class="text-uppercase">@lang('app.in progress')</h6>
    <p>{{ $inProgressBookings }}</p>
</div>

<div class="col-md-2 text-center mt-3 border-right">
    <h6 class="text-uppercase">@lang('app.pending')</h6>
    <p>{{ $pendingBookings }}</p>
</div>

<div class="col-md-2 text-center mt-3 border-right">
    <h6 class="text-uppercase">@lang('app.canceled')</h6>
    <p>{{ $canceledBookings }}</p>
</div>

<div class="col-md-2 text-center mt-3">
    <h6 class="text-uppercase">@lang('modules.booking.earning')</h6>
    <p>{{ $settings->currency->currency_symbol }}{{ round($earning, 2) }}</p>
</div>
