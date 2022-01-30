<div class="modal-header">
    <h4 class="modal-title">@lang('app.edit') @lang('app.language')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form role="form" id="editLangForm"  class="ajax-form" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $language->id }}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.language') @lang('app.name')</label>
                    <input type="text" name="language_name" id="language_name" class="form-control form-control-lg" value="{{ $language->language_name }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.language') @lang('app.code')</label>
                    <input type="text" name="language_code" id="language_code" class="form-control form-control-lg" value="{{ $language->language_code }}">
                </div>
            </div>
            @if ($language->language_code !== $settings->locale)
                <div class="col-md-12">
                    <div class="form-group">
                        <label>@lang('app.language') @lang('app.status')</label>

                        <select name="status" id="lang-status" class="form-control form-control-lg">
                            <option @if ($language->status == 'enabled')
                                selected
                            @endif value="enabled">@lang('app.enabled')</option>
                            <option @if ($language->status == 'disabled')
                                selected
                            @endif value="disabled">@lang('app.disabled')</option>
                        </select>
                    </div>
                </div>
            @else
                <input id="lang-status" type="hidden" name="status" value="enabled">
            @endif
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="updateLangForm" data-row-id="{{ $language->id }}" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>


