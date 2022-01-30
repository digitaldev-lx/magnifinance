<style>
    .grecaptcha-badge {
        width: 70px !important;
        overflow: hidden !important;
        transition: all 0.3s ease !important;
        left: 2px !important;
    }
    .grecaptcha-badge:hover {
        width: 256px !important;
    }
</style>
<div class="modal-header">
    <h5 class="modal-title">@lang('menu.verifyGoogleRecaptchaV3')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
​
<div class="modal-body">
    <div class="portlet-body" id="portlet-body" data-error="false">
        <div class="alert alert-primary" role="alert"><i class="fa fa-info-circle"></i>
            @lang('pleaseWait')...! @lang('keyHasBeenVerifying').
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" class="btn btn-success btn-light-round g-recaptcha" id="save-method" data-sitekey="{{ $key }}"
        data-callback='saveForm' data-error-callback="errorMsg">
        <i class="fa fa-check" aria-hidden="true"></i>@lang('app.submit')
    </button>
</div>
​
<script src="https://www.google.com/recaptcha/api.js"></script>
<script>
    setTimeout(() => {
        if ($('#portlet-body').data('error') !== true) {
            let msg = `<div class="alert alert-success" type="success">
            Key has been verified. Click on save button to save key.
            <i class="fa fa-info-circle"></i></div>`;
            $('#portlet-body').html(msg);
            $('#save-method').show();
        }
    }, 3000);
</script>
