<div class="modal-header">
    <h4 class="modal-title">@lang('modules.offlinePayment.update')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form role="form" id="editForm" class="ajax-form" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.offlinePayment.name')</label>
                    <input type="text" name="name" id="name" class="form-control form-control-lg"
                        value="{{ $method->name }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.offlinePayment.description')</label>
                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control"
                        rows="4">{{ $method->description }}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.offlinePayment.status')</label>
                    <select class="form-control form-control-lg" name="status" id="status">
                        <option value="yes" @if ($method->status === 'yes') selected @endif>@lang('app.active')</option>
                        <option value="no" @if ($method->status === 'no') selected @endif>@lang('app.deactive')</option>
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
        const form = $('#editForm');

        $.easyAjax({
            url: '{{ route('superadmin.payment-settings.update', $method->id) }}',
            container: '#editForm',
            type: "PUT",
            redirect: true,
            data: form.serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    $(modal_lg).modal('hide');
                    table._fnDraw();
                }
            }
        })
    });

</script>
