<style>
    .input-group[class*=col-] {
        padding-right: 7px !important;
        padding-left: 8px !important;
    }
</style>

<div id="event-detail">
    <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-cash"></i> @lang('modules.payments.paymentDetails')</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
        <div class="form-body">
            <form id="saveDetailPayment" class="ajax-form " method="POST">
                <div class="row">{{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="exampleInputPassword1">@lang('app.slip')</label>
                            <div class="card">
                                <div class="card-body">
                                    <input type="file" id="input-file-now" name="slip"  class="dropify" />
                                </div>
                            </div>
                        </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3">@lang('app.description')</label>

                            <textarea class="form-control" rows="4" name="description"></textarea>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="package_id" value="{{ $package_id }}">
                <input type="hidden" name="offline_id" value="{{ $offlineId }}">
                <input type="hidden" name="type" value="{{ $type }}">
            </form>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
            @lang('app.cancel')</button>
        <button type="button" id="save-details" class="btn btn-success"><i class="fa fa-check"></i>
            @lang('app.submit')</button>
    </div>
</div>

<script>
    $('body').on('click', '#save-details', function() {
        e.preventDefault();

        $.easyAjax({
            url: '{{ route('admin.billing.offline-payment-submit') }}',
            type: "POST",
            container:'#saveDetailPayment',
            messagePosition:'inline',
            file:true
        });
    });
</script>

