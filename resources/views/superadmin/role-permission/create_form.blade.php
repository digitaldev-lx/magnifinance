<form id="create-role" class="ajax-form">
    @csrf
    <div class="form-body">
        <h5>@lang('modules.rolePermission.addRole')</h5>
        <div class="row">
            <div class="col-sm-12 ">
                <div class="form-group">
                    <label>@lang('modules.rolePermission.forms.displayName')</label>
                    <input type="text" name="display_name" id="display_name" class="form-control">
                </div>
            </div>
            <div class="col-sm-12 ">
                <div class="form-group">
                    <label>@lang('modules.rolePermission.forms.description')</label>
                    <input type="text" name="description" id="description" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions text-right">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
            @lang('app.cancel')</button>
        <button type="button" id="save-create-role" class="btn btn-success"> <i class="fa fa-check"></i>
            @lang('app.submit')</button>
    </div>
</form>
