<div class="modal-header">
    <h4 class="modal-title">@lang('modules.offlinePayment.createNew')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form role="form" id="createForm" class="ajax-form" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.offlinePayment.name')</label>
                    <input type="text" name="name" id="name" class="form-control form-control-lg" value="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.offlinePayment.description')</label>
                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control"
                        rows="4"></textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.offlinePayment.status')</label>
                    <select class="form-control form-control-lg" name="status" id="status">
                        <option value="yes">@lang('app.active')</option>
                        <option value="no">@lang('app.deactive')</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>


<script>
    $('body').on('click', '#save-form', function() {
        const form = $('#createForm');

        $.easyAjax({
            url: '{{ route('superadmin.payment-settings.store') }}',
            container: '#createForm',
            type: "POST",
            redirect: true,
            data: form.serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    $(modal_lg).modal('hide');

                    if (typeof table != 'undefined') {
                        table._fnDraw();
                    } else {
                        $('#paymentMethod').append(
                            `<option value="${response.method.id}">${response.method.name.toUpperCase()}</option>`
                            )
                    }
                }
            }
        })
    });

</script>
