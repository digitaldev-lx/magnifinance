<style>
    #reportrange {
        background: #fff;
        cursor: pointer;
        padding: 15px 20px;
        border: 1px solid #ccc;
        width: 100%
    }
    #reportmonth {
        background: #fff;
        cursor: pointer;
        padding: 9px 15px;
        border: 1px solid #ccc;
    }
    #toutesRevenueChart {
        height: 400px !important;
    }
    #toutesRevenueTable {
        width: 100% !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-6">
                        <h6>@lang('app.dateRange') </h6>
                        <div id="reportrange" class="form-group">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down pull-right"></i>
                            <input type="hidden" id="start-date">
                            <input type="hidden" id="end-date">
                        </div>
                    </div>
                </div>
                <!-- Custom Tabs -->
                <div class="card">
                    <div class="card-header d-flex p-0">
                        <h3 class="card-title p-3">{{__('menu.toutesRevenue')}}</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div id="toutes-revenue-graph-container">
                                    <canvas id="toutesRevenueChart"></canvas>
                                </div> <hr>
                                <div class="table-responsive">
                                    <table id="toutesRevenueTable" class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('app.company') @lang('app.logo')</th>
                                                <th>@lang('app.company') @lang('app.name')</th>
                                                <th>@lang('app.company') @lang('app.owner')</th>
                                                <th>@lang('app.company') {{__('app.avertising_expenses')}}</th>
                                                <th>@lang('app.paid') @lang('app.amount')</th>
                                                <th>@lang('app.paid_on')</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- /myTable -->
                        </div>
                        <!-- /.carmyTable -->
                    </div>
                    <!-- ./card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
    </div>
</div>

@push('footer-js')
    <script>
        $(function() {
            var start = moment().subtract(90, 'days');
            var end = moment();

            function renderTranslatedNames() {
                @foreach($labels as $key => $label)
                    $(`.daterangepicker li[data-range-key='{{ $key }}']`).html("@lang('app.daterangepicker.'.$label)");
                @endforeach
            }

            function cb(start, end) {
                $('#reportrange span').html('{{ \Carbon\Carbon::now()->subDays(90)->translatedFormat($settings->date_format) }} - {{ \Carbon\Carbon::now()->translatedFormat($settings->date_format) }}');
                $('#start-date').val(start.format('YYYY-MM-DD'));
                $('#end-date').val(end.format('YYYY-MM-DD'));

                chartRequest(
                    '{{ route("superadmin.reports.toutesRevenueChart") }}',
                    {
                        startDate: $('#start-date').val(),
                        endDate: $('#end-date').val(),
                    },
                    'toutesRevenueChart',
                    'toutes-revenue-graph-container',
                    '@lang("menu.toutesRevenue")',
                );

                renderTable(
                    'toutesRevenueTable',
                    '{!! route('superadmin.reports.toutesRevenueTable') !!}', {
                        "startDate": $('#start-date').val(),
                        "endDate": $('#end-date').val(),
                    },
                    [
                        { data: 'company_logo', name: 'company_logo' },
                        { data: 'company', name: 'company' },
                        { data: 'company_owner', name: 'company_owner' },
                        { data: 'avertising_expenses', name: 'avertising_expenses' },
                        { data: 'amount', name: 'amount' },
                        { data: 'paid_on', name: 'paid_on' },
                    ]
                );
            }

            moment.locale('{{ $settings->locale }}');

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                locale: {
                    format: "MM/DD/YYYY",
                    separator: " - ",
                    applyLabel: "@lang('app.apply')",
                    cancelLabel: "@lang('app.cancel')",
                    customRangeLabel: "@lang('app.daterangepicker.custom')"
                },
                ranges: {
                '@lang("app.daterangepicker.today")': [moment(), moment()],
                '@lang("app.daterangepicker.yesterday")': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '@lang("app.daterangepicker.lastWeek")': [moment().subtract(6, 'days'), moment()],
                '@lang("app.daterangepicker.lastThirtyDays")': [moment().subtract(29, 'days'), moment()],
                '@lang("app.daterangepicker.thisMonth")': [moment().startOf('month'), moment().endOf('month')],
                '@lang("app.daterangepicker.lastMonth")': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            },
            cb);

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                $('#reportrange span').html(picker.startDate.format('{{ $date_picker_format }}') + ' - ' + picker.endDate.format('{{ $date_picker_format }}'));
            });

            cb(start, end);

            renderTranslatedNames();

        });
    </script>
@endpush
