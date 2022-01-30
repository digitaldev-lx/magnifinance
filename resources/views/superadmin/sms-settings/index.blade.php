<h4>@lang('app.smsCredentials') @lang('menu.settings')</h4>
<br>
<form class="form-horizontal ajax-form" id="sms-setting-form" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12 ">
            <h5 class="text-info">@lang('app.nexmoCredential') </h5>
            <div class="form-group">
                <label class="control-label">
                    @lang("modules.nexmoCredential.status")
                </label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="nexmo_status" id="nexmo_status" @if ($smsSetting->nexmo_status == 'active') checked @endif
                        value="active" onchange="toggle('#nexmo-credentials');">
                    <span class="slider round"></span>
                </label>
            </div>
            <div id="nexmo-credentials">
                <div class="form-group">
                    <label>@lang("modules.nexmoCredential.key")</label>
                    <input type="text" name="nexmo_key" id="nexmo_key" class="form-control form-control-lg"
                        value="{{ $smsSetting->nexmo_key }}">
                </div>
                <div class="form-group">
                    <label>@lang("modules.nexmoCredential.secret")</label>
                    <input type="password" name="nexmo_secret" id="nexmo_secret" class="form-control form-control-lg"
                        value="{{ $smsSetting->nexmo_secret }}">
                </div>
                <div class="form-group">
                    <label>@lang("modules.nexmoCredential.from")</label>
                    <input type="text" name="nexmo_from" id="nexmo_from" class="form-control form-control-lg"
                        value="{{ $smsSetting->nexmo_from }}">
                </div>
            </div>
        </div>
        <div class="col-12 border-top mb-3"></div>
        <div class="col-md-12">
            <h5 class="text-info">@lang('app.msg91Credential') </h5>
            <div class="form-group">
                <label class="control-label">
                    @lang("modules.msg91Credential.status")
                </label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="msg91_status" id="msg91_status" @if ($smsSetting->msg91_status == 'active') checked @endif
                        value="active" onchange="toggle('#msg91-credentials');">
                    <span class="slider round"></span>
                </label>
            </div>
            <div id="msg91-credentials">
                <div class="form-group">
                    <label>@lang("modules.msg91Credential.key")</label>
                    <input type="text" name="msg91_key" id="msg91_key" class="form-control form-control-lg"
                        value="{{ $smsSetting->msg91_key }}">
                </div>
                <div class="form-group">
                    <label>@lang("modules.msg91Credential.from")</label>
                    <input type="text" name="msg91_from" id="msg91_from" class="form-control form-control-lg"
                        value="{{ $smsSetting->msg91_from }}">
                </div>
            </div>
        </div>
        <div class="col-md-12 ">
            <div class="form-group">
                <button id="save-sms-settings" type="button" class="btn btn-success"><i class="fa fa-check"></i>
                    @lang('app.save')</button>
            </div>
        </div>
        <!--/span-->
    </div>
</form>
