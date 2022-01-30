<div class="tab-pane" id="bookingsInvoices">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="bookingInvoicesTable" class="table w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('app.company')</th>
                            <th>@lang('app.transactionId')</th>
                            <th>@lang('app.amount')</th>
                            <th>@lang('app.application_fee')</th>
                            <th>@lang('app.method')</th>
                            <th>@lang('app.paid_on')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="tab-pane active" id="subcriptionInvoices">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table w-100">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('app.company')</th>
                        <th>@lang('app.package')</th>
                        <th>@lang('modules.payments.transactionId')</th>
                        <th>@lang('app.amount')</th>
                        <th>@lang('app.date')</th>
                        <th>@lang('modules.billing.nextPaymentDate')</th>
                        <th>@lang('modules.payments.paymentGateway')</th>
                        <th class="text-right">@lang('app.action')</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

