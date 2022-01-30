<div class="modal-header">
    <h4 class="modal-title">@lang('app.createNew') @lang('app.language')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form role="form" id="createLangForm" class="ajax-form" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>@lang('app.language') @lang('app.name')</label>
                    <input type="text" name="language_name" id="language_name" class="form-control form-control-lg" value="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.language') @lang('app.code')</label>
                    <input type="text" name="language_code" id="language_code" class="form-control form-control-lg" value="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.language') @lang('app.status')</label>

                    <select name="status" id="lang-status" class="form-control form-control-lg">
                        <option value="enabled">@lang('app.enabled')</option>
                        <option value="disabled">@lang('app.disabled')</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="saveLangForm" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>
