@if ($superadmin->google_calendar == 'active')
    @push('head-css')
        <style>
            .removeNotification {
                padding: 9px;
                margin-top: 7px;
            }
            .status-badge {
                border-radius: 100px;
            }

        </style>
    @endpush
    @if (Session::has('message'))
        <p class="alert alert-success">{{ Session::get('message') }}</p>
    @endif
    <div class="row">
        <div class="col-md-6">
            <h5 class="text-info">@lang('menu.googleCalendar')</h5>
            <a href="{{ route('googleAuth') }}"> <button type="button" class="btn btn-success  mt-1">
                    <i class="fa fa-play"></i>
                    @if (auth()->user()->googleAccount) @lang('app.change')
                        @lang('menu.googleCalendar') @lang('app.account')
                    @else @lang('app.connect') @lang('menu.googleCalendar') @lang('app.account')@endif
                </button>
            </a>
            @if (auth()->user()->googleAccount)
                <button type="button" id="googleCalendarDisconnect" class="btn btn-danger mt-1">
                    @lang('app.disconnect') @lang('menu.googleCalendar') </button>

            @endif
        </div>

        <div class="col-md-3">
            <h5 class="text-info">@lang('app.status')</h5>
            <div class="form-group">
                <span
                    class="badge status-badge {{ auth()->user()->googleAccount ? 'badge-success' : 'badge-danger' }}">{{ auth()->user()->googleAccount ? __('app.connected') : __('app.notConnected') }}</span>
            </div>
        </div>

    </div>
    @if (auth()->user()->googleAccount)
        <br>
        <form class="form-horizontal ajax-form" id="bookingNotificationForm" method="POST">
            @csrf
            <div class="field_wrapper">
                @foreach ($companyBookingNotification as $notifaction)
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="duration" class="control-label">@lang("app.duration")</label>
                                <input type="number" class="form-control form-control-lg" name="duration[]" min="1"
                                    value="{{ $notifaction->duration }}">
                            </div>
                        </div>
                        <div class="col-5">
                            <label for="duration_type" class="control-label">@lang("app.durationType")</label>
                            <select name="duration_type[]" class="form-control form-control-lg">
                                <option value="minutes"
                                    {{ $notifaction->duration_type == 'minutes' ? 'selected' : '' }}>
                                    @lang("app.minutes")
                                </option>
                                <option value="hours" {{ $notifaction->duration_type == 'hours' ? 'selected' : '' }}>
                                    @lang("app.hours")
                                </option>
                                <option value="days" {{ $notifaction->duration_type == 'days' ? 'selected' : '' }}>
                                    @lang("app.days")
                                </option>
                                <option value="weeks" {{ $notifaction->duration_type == 'weeks' ? 'selected' : '' }}>
                                    @lang("app.weeks")
                                </option>
                            </select>
                        </div>
                        <div class="col-1 pt-4">
                            <a href="javascript:;" class="btn btn-danger btn-sm btn-circle removeNotification"
                                data-row-id="{{ $notifaction->id }}"><i class="fa fa-times"
                                    aria-hidden="true"></i></a>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="row addNotification {{ $companyBookingNotification->count() >= 2 ? 'd-none' : '' }}">
                <button type="button" id="addNotification" class="btn btn-link">@lang("app.addMoreMotification")</button>
            </div>
            <div id="bookingNotificationFormBtn"
                class="row {{ $companyBookingNotification->count() == 0 ? 'd-none' : '' }}">
                <div class="col-md-12">
                    <div class="form-group">
                        <button id="saveBookingNotificationForm" type="button" class="btn btn-success"><i
                                class="fa fa-check"></i> @lang('app.save')</button>
                    </div>
                </div>
            </div>
        </form>
    @endif
@else
<div class="row alert alert-warning m-0">
    <div class="col-md-12 d-flex align-items-center">@lang("app.superAdminAllowCalendarMessage") </div>
</div>
@endif
