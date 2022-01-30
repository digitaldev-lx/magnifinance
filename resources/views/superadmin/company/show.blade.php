
<div class="modal-header">
    <h5 class="modal-title">@lang('app.companyDetails')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="row">
        <br><br><br>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.company') @lang('app.name')</span> <br>
            <p class="text-muted">{{ ucwords($company->company_name) }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.company') @lang('app.email')</span> <br>
            <p class="text-muted">{{ $company->company_email }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.company') @lang('app.phone')</span> <br>
            <p class="text-muted">{{ $company->company_phone }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.company') @lang('app.website')</span> <br>
            <p class="text-muted">{{ $company->website }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.company') @lang('app.address')</span> <br>
            <p class="text-muted">{{ ucwords($company->address) }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.default') @lang('app.currency')</span> <br>
            <p class="text-muted">{{ $company->currency->currency_symbol }}
                {{ $company->currency->currency_code }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.default') @lang('app.timezone')</span> <br>
            <p class="text-muted">{{ $company->timezone }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.status')</span> <br>
            <p class="text-muted">
                <label class="badge @if ($company->status == 'active') badge-success
                @else badge-danger @endif">{{ ucwords($company->status) }}</label>
            </p>
        </div>
    </div><hr>
    <div class="row">
        <div class="col-md-12">
            <h6>@lang('app.packageDetail')</h6>
        </div><br>

        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.package') @lang('app.name')</span> <br>
            <p class="text-muted">{{ ucwords($company->package->name) }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.employees') @lang('app.quota')</span> <br>
        <p class="text-muted">{{ $employees }} / {{ $company->package->max_employees }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.price')</span> <br>
            <p class="text-muted">{{ $company->package->monthly_price }}</p>
        </div>
        <div class="col-md-6">
            <span class="font-semi-bold">@lang('app.licence') @lang('app.expires') @lang('app.on')</span> <br>
            <p class="text-muted">{{ \Carbon\Carbon::now()->add(1, 'year')->format($settings->date_format) }}</p>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
        @if ($user->hasRole('superadmin') || $user->roles()->withoutGlobalScopes()->first()->hasPermission('update_company', 'delete_company'))
            <a href="{{route('superadmin.companies.edit', [$company->id])}}" class="btn btn-success"><i class="fa fa-pencil"></i>
                @lang('app.edit') @lang('app.company')</a>
        @endif
</div>
