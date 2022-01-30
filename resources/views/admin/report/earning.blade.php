<style>
    #reportrange {
        background: #fff;
        cursor: pointer;
        padding: 15px 20px;
        border: 1px solid #ccc;
        width: 100%
    }
    #earningreportlocation {
        background: #fff;
        cursor: pointer;
        padding: 9px 15px;
        border: 1px solid #ccc;
    }
    #earning_report_location {
        width:100%; border: none;
    }
    #earningChart {
        height: 400px !important;
    }
    #earningTable {
        width: 100%;
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
                    <div class="col-md-4">
                        <h6>@lang('app.location')</h6>
                        <div id="earningreportlocation" class="form-group">
                            <select class="form-control" name="earning_report_location" id="earning_report_location">
                                @foreach ($locations as $location)
                                    <option value="{{$location->id}}">{{$location->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Custom Tabs -->
                <div class="card">
                    <div class="card-header d-flex p-0">
                        <h3 class="card-title p-3">@lang('menu.earningReport')</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div id="earning-graph-container">
                                    <canvas id="earningChart"></canvas>
                                </div> <hr>
                                <div class="table-responsive">
                                    <table id="earningTable" class="table">
                                        <thead>
                                            <tr>
                                                <th>@lang('app.booking') #</th>
                                                <th>@lang('app.customer')</th>
                                                <th>@lang('app.amount')</th>
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
                    '{{ route("admin.reports.earningReportChart") }}',
                    {
                        startDate: $('#start-date').val(),
                        endDate: $('#end-date').val(),
                        location : $('#earning_report_location').val()
                    },
                    'earningChart',
                    'earning-graph-container',
                    '@lang("app.amount")'
                );
                renderTable(
                    'earningTable',
                    '{!! route('admin.reports.earningTable') !!}', {
                        "startDate": $('#start-date').val(),
                        "endDate": $('#end-date').val(),
                        "location": $('#earning_report_location').val(),
                    },
                    [
                        { data: 'user_id', name: 'user_id' },
                        { data: 'amount_to_pay', name: 'amount_to_pay' },
                        { data: 'date_time', name: 'date_time' }
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

            $('body').on('change', '#earning_report_location', function() {
                cb(start, end);
            });

        });
    </script>
@endpush
