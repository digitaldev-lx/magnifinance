<div class="d-flex justify-content-center justify-content-md-end mb-3">
    <a href="javascript:;" id="create-language" class="btn btn-rounded btn-primary mb-1 mr-2">
        <i class="fa fa-plus"></i> @lang('app.createNew')
    </a>
    <a href="{{ url('/translations') }}" target="_blank" id="translations" class="btn btn-rounded btn-warning mb-1">
        <i class="fa fa-cog"></i> @lang('app.translations')
    </a>
</div>
<div class="table-responsive">
    <table id="langTable" class="table w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('app.name')</th>
                <th>@lang('app.code')</th>
                <th>@lang('app.status')</th>
                <th class="text-right">@lang('app.action')</th>
            </tr>
        </thead>
    </table>
</div>
