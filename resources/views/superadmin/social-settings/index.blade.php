<h4>@lang('app.socialLogin') @lang('menu.settings')</h4>
<br>
<form class="form-horizontal ajax-form" id="social-login-form" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12 ">
            <h5 class="text-info">@lang('app.googleCredential') </h5>
            <div class="form-group">
                <label class="control-label">
                    @lang("app.google") @lang("app.status")
                </label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="google_status" id="google_status" @if ($socialCredentials->google_status == 'active') checked @endif
                        value="active" onchange="toggle('#google-credentials');">
                    <span class="slider round"></span>
                </label>
            </div>
            <div id="google-credentials">
                <div class="form-group">
                    <label>@lang('app.socialAuthSettings.googleClientId')</label>
                    <input type="text" name="google_client_id" id="google_client_id" class="form-control form-control-lg"
                        value="{{ $socialCredentials->google_client_id }}">
                </div>
                <div class="form-group">
                    <label>@lang('app.socialAuthSettings.googleSecret')</label>
                    <input type="password" name="google_secret_id" id="google_secret_id" class="form-control form-control-lg"
                        value="{{ $socialCredentials->google_secret_id }}">
                </div>
                <div class="form-group">
                    <label for="mail_from_name">@lang('app.callback')</label>
                    <p class="text-bold">{{ route('social.login-callback', 'google') }}</p>
                    <p class="text-info">(@lang('messages.addGoogleCallback'))</p>
                </div>
            </div>
        </div>
        <div class="col-12 border-top mb-3"></div>
        <div class="col-md-12">
            <h5 class="text-info">@lang('app.facebookCredential') </h5>
            <div class="form-group">
                <label class="control-label">
                    @lang("app.facebook") @lang("app.status")
                </label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="facebook_status" id="facebook_status" @if ($socialCredentials->facebook_status == 'active') checked @endif
                        value="active" onchange="toggle('#facebook-credentials');">
                    <span class="slider round"></span>
                </label>
            </div>
            <div id="facebook-credentials">
                <div class="form-group">
                    <label>@lang("app.socialAuthSettings.facebookClientId")</label>
                    <input type="text" name="facebook_client_id" id="facebook_client_id" class="form-control form-control-lg"
                        value="{{ $socialCredentials->facebook_client_id }}">
                </div>
                <div class="form-group">
                    <label>@lang("app.socialAuthSettings.facebookSecret")</label>
                    <input type="text" name="facebook_secret_id" id="facebook_secret_id" class="form-control form-control-lg"
                        value="{{ $socialCredentials->facebook_secret_id }}">
                </div>
                <div class="form-group">
                    <label for="mail_from_name">@lang('app.callback')</label>
                    <p class="text-bold">{{ route('social.login-callback', 'facebook') }}</p>
                    <p class="text-info">(@lang('messages.addFacebookCallback'))</p>
                </div>
            </div>
        </div>
        <div class="col-md-12 ">
            <div class="form-group">
                <button id="save-social-settings" type="button" class="btn btn-success"><i class="fa fa-check"></i>
                    @lang('app.save')</button>
            </div>
        </div>
        <!--/span-->
    </div>
</form>
