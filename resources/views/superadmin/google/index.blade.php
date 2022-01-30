<h4>@lang('menu.googleCalendar') @lang('menu.configuration')</h4>

<hr>
<form class="form-horizontal ajax-form" id="googleCalendarConfigForm" method="POST">
@csrf
<div class="row">
    <div class="col-md-12">
        <h5 class="text-secondary">@lang('app.showGoogleCalendarOption')</h5>
            <div class="form-group">
                <label class="control-label">@lang("app.allowGoogleCalendarOption")</label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="google_calendar" id="google_calendar"
                    {{$settings->google_calendar == 'active'?'checked':''}} value="active">
                    <span class="slider round"></span>
                </label>
            </div>
    </div>
    <div class="col-md-12 {{$settings->google_calendar == 'deactive'?'d-none':''}}" id="google_calendar_config_option">
        <div class="form-group">
            <label for="google_client_id" class="control-label">@lang('app.ClientId')</label>
            <input type="text" class="form-control  form-control-lg"
            id="google_client_id" name="google_client_id"
            value="{{ $settings->google_client_id }}">
        </div>
        <div class="form-group">
            <label for="google_client_secret" class="control-label">@lang('app.ClientSecret')</label>
            <input type="password" class="form-control  form-control-lg"
            id="google_client_secret" name="google_client_secret"
            value="{{ $settings->google_client_secret }}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <button id="saveGoogleCalendarConfigForm" type="button"
            class="btn btn-success"><i class="fa fa-check"></i>
            @lang('app.save')</button>
        </div>
    </div>
</div>
</form>
