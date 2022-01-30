<div class="modal-header">
    <h4 class="modal-title">@lang("app.testEmail")</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form role="form" id="testEmail" class="ajax-form" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>@lang("app.testEmailLable")</label>
                    <input type="text" name="test_email" id="test_email" class="form-control form-control-lg" value="{{ auth()->user()->email }}">
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="sendTestEmailSubmit" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>

