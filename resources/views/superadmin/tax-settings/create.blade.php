<div class="modal-header">
    <h4 class="modal-title">@lang('app.add') @lang('app.tax')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form class="form-horizontal ajax-form" id="tax-form" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="tax_name" class="control-label">@lang('app.tax') @lang('app.name')</label>

                    <input type="text" class="form-control form-control-lg" id="tax_name" name="tax_name">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="percent" class="control-label">@lang('app.percent')</label>

                    <input type="number" min="0" step="0.01" class="form-control form-control-lg" id="percent"
                        name="percent">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="percent" class="control-label">@lang('app.status')</label>
                    <select name="status" id="status" class="form-control form-control-lg">
                        <option value="active">@lang('app.active')</option>
                        <option value="inactive">@lang('app.inactive')</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" onclick="saveTax()" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>


<script>
    function saveTax() {
        $.easyAjax({
            url: '{{ route('superadmin.tax-settings.store') }}',
            container: '#tax-form',
            type: "POST",
            data: $('#tax-form').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    $.unblockUI();
                    $(modal_default).modal('hide');
                    taxTable._fnDraw();
                }
            }
        })
    }

</script>
