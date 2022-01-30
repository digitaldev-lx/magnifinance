<style>
    .dropify-wrapper,
    .dropify-preview,
    .dropify-render img {
        background-color: var(--sidebar-bg) !important;
    }

</style>

<div class="modal-header">
    <h5>@lang('menu.frontPopularStoreSettings')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <section class="mt-3 mb-3">
        <form class="form-horizontal ajax-form" id="addPopularStoreForm" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <h6 class="col-md-12 text-primary">@lang('app.select') @lang('app.company')</h6>
                        <select name="store_id" id="store_id" class="form-control  form-control-lg">
                            <option value=""></option>
                            @foreach ($stores as $store)
                                @if ($store->popular_store != 1)
                                    <option value="{{ $store->id }}">{{ $store->company_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="savePopularStoreForm" class="btn btn-success btn-light-round"><i
        class="fa fa-check"></i> @lang('app.submit')</button>
</div>
