<style>
    #canvas-holder {
        width:100%;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <!-- Custom Tabs -->
                <div class="card">
                    <div class="card-header d-flex p-0">
                        <h3 class="card-title p-3">@lang('report.charts')</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" id="accordion">
                        <ul class="chart_menu">
                            <li class="orange chart_menu_li">
                                <a id="getUserTypeChart" href="javascript:;">
                                    @lang('report.userTypeChart')
                                </a>
                            </li>
                            <li class="yellow chart_menu_li">
                                <a id="serviceTypeChart" href="javascript:;">
                                    @lang('report.serviceTypeChart')
                                </a>
                            </li>
                            <li class="teal chart_menu_li">
                                <a id="bookingSourceChart" href="javascript:;">
                                    @lang('report.bookingSource')
                                </a>
                            </li>
                            <li class="cyan chart_menu_li">
                                <a class="collapsed chart-link" data-toggle="collapse" href="#sub_li_booking_per_day" id="bookingPerDay">
                                    @lang('report.bookingPerDay')
                                </a>
                            </li>
                            <li class="light chart_menu_li collapse" data-parent="#accordion" id="sub_li_booking_per_day">
                                <div class="row">
                                <div class="col-1"></div>
                                <div class="col-md-8">
                                    <div class="datePicker">
                                        <input type="text" class="form-control" id="booking">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button id="bookingPerDayButton" class="btn btn-success"><i class="fa fa-search"></i></button>
                                </div>
                                </div>
                            </li>
                            <li class="gray chart_menu_li">
                                <a class="collapsed chart-link" data-toggle="collapse" href="#sub_li_booking_wise_month" id="bookingPerMonth">
                                    @lang('report.bookingPerMonth')
                                </a>
                            </li>
                            <li class="light chart_menu_li collapse" data-parent="#accordion" id="sub_li_booking_wise_month">
                                <div class="row">
                                <div class="col-1"></div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control reportmonth" id="booking_month">
                                </div>
                                <div class="col-md-2">
                                    <button id="bookingPerMonthButton" class="btn btn-success"><i class="fa fa-search"></i></button>
                                </div>
                                </div>
                            </li>
                            <li class="primary chart_menu_li">
                                <a class="collapsed chart-link" data-toggle="collapse" href="#sub_li_booking_per_year" id="bookingPerYear">
                                    @lang('report.bookingPerYear')
                                </a>
                            </li>
                            <li class="light chart_menu_li collapse" data-parent="#accordion"  id="sub_li_booking_per_year">
                                <div class="row">
                                <div class="col-1"></div>
                                <div class="col-md-8">
                                    <input id="booking_year" type="text" class="form-control yearPicker">
                                </div>
                                <div class="col-md-2">
                                    <button id="bookingPerYearButton" class="btn btn-success"><i class="fa fa-search"></i></button>
                                </div>
                                </div>
                            </li>
                            <li class="info chart_menu_li">
                                <a id="paymentPerDay" class="collapsed chart-link" data-toggle="collapse" href="#sub_li_payment_collection_per_day">
                                    @lang('report.PaymentCollectionPerDay')
                                </a>
                            </li>
                            <li class="light chart_menu_li collapse" data-parent="#accordion"  id="sub_li_payment_collection_per_day">
                                <div class="row">
                                <div class="col-1"></div>
                                <div class="col-md-8">
                                    <div class="datePicker">
                                        <input type="text" class="form-control" id="payment_date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button id="paymentPerDayButton" class="btn btn-success"><i class="fa fa-search"></i></button>
                                </div>
                                </div>
                            </li>
                            <li class="warning chart_menu_li">
                                <a id="paymentCollectionPerMonth" class="collapsed chart-link" data-toggle="collapse" href="#sub_li_payment_collection_per_month">
                                    @lang('report.paymentCollectionPerMonth')
                                </a>
                            </li>
                            <li class="light chart_menu_li collapse" data-parent="#accordion" id="sub_li_payment_collection_per_month">
                                <div class="row">
                                <div class="col-1"></div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control reportmonth" id="payment_month">
                                </div>
                                <div class="col-md-2">
                                    <button id="paymentPerMonthButton" class="btn btn-success"><i class="fa fa-search"></i></button>
                                </div>
                                </div>
                            </li>
                            <li class="danger chart_menu_li">
                                <a id="paymentCollectionPerYear" class="collapsed chart-link" data-toggle="collapse" href="#sub_li_payment_collection_per_year">
                                    @lang('report.paymentCollectionPerYear')
                                </a>
                            </li>
                            <li class="light chart_menu_li collapse" data-parent="#accordion" id="sub_li_payment_collection_per_year">
                                <div class="row">
                                <div class="col-1"></div>
                                <div class="col-md-8">
                                    <input id="payment_year" type="text" class="form-control yearPicker">
                                </div>
                                <div class="col-md-2">
                                    <button id="paymentPerYearButton" class="btn btn-success"><i class="fa fa-search"></i></button>
                                </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        <div id="canvas-holder">
                            <canvas id="chart-area"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('footer-js')
    <script src="{{ asset('js/utils.js') }}"></script>
    <script>

        let hidden_booking_date = '';
        let hidden_payment_date = '';

        $( document ).ready(function() {
            $(function() {
                toastr.options = {
                    "progressBar": false,
                    "positionClass": "toast-bottom-right",
                    "preventDuplicates": true
                };
            });

            $('#getUserTypeChart').click();

            $(function () {

                $('#booking').datetimepicker({
                    format: '{{ $date_picker_format }}',
                    locale: '{{ $settings->locale }}',
                    allowInputToggle: true,
                    icons: {
                        time: "fa fa-clock-o",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down",
                        previous: "fa fa-angle-double-left",
                        next: "fa fa-angle-double-right"
                    }
                }).on('dp.change', function(e) {
                    hidden_booking_date = moment(e.date).format('YYYY-MM-DD');
                });

                $('#payment_date').datetimepicker({
                    format: '{{ $date_picker_format }}',
                    locale: '{{ $settings->locale }}',
                    allowInputToggle: true,
                    icons: {
                        time: "fa fa-clock-o",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down",
                        previous: "fa fa-angle-double-left",
                        next: "fa fa-angle-double-right"
                    }
                }).on('dp.change', function(e) {
                    hidden_payment_date= moment(e.date).format('YYYY-MM-DD');
                });

                $('.reportmonth').datepicker({
                    format: "yyyy-mm",
                    language: "{{ $settings->locale }}",
                    autoclose: true,
                    viewMode: "months",
                    minViewMode: "months"
                });

                $('.yearPicker').datepicker({
                    minViewMode: 2,
                    format: 'yyyy',
                    language: "{{ $settings->locale }}",
                    autoclose: true,
                    icons: {
                        time: "fa fa-clock-o",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down"
                    }
                });

            });
        });

        $('body').on('click', '.chart_menu li a', function() {
            $(".chart_menu>li>a").css("background", "#F7F5F2");
            $(this).css("background", "#b7bfde");
        });

        $('body').on('click', '#getUserTypeChart', function() {
            $.ajax({
                  url: "{{ route('admin.reports.userTypeChart') }}",
                  method: 'get',
                  success: function(result)
                  {
                    pie_chart_config(result.data.data, result.data.label, '@lang("report.userTypeChart")');
                  }
            });
        });

        $('body').on('click', '#serviceTypeChart', function() {
            $.ajax({
                  url: "{{ route('admin.reports.serviceTypeChart') }}",
                  method: 'get',
                  success: function(result)
                  {
                    pie_chart_config(result.data.data, result.data.label, '@lang("report.serviceTypeChart")');
                  }
            });
        });

        $('body').on('click', '#bookingSourceChart', function() {
            $.ajax({
                  url: "{{ route('admin.reports.bookingSourceChart') }}",
                  method: 'get',
                  success: function(result)
                  {
                    pie_chart_config(result.data.data, result.data.label, '@lang("report.bookingSource")');
                  }
            });
        });

        $('body').on('click', '#bookingPerDayButton', function() {
            var booking_date = $('#booking').val();
            if(hidden_booking_date==='')
            {
                $('#booking').focus();
                return toastr.error('@lang("report.enterBookingDate")');
            }
            $.ajax({
                  url: "{{ route('admin.reports.bookingPerDayChart') }}",
                  method: 'post',
                  data: {booking_date:hidden_booking_date, _token:"{{ csrf_token() }}"  },
                  success: function(result)
                  {
                        pie_chart_config(result.data.data, result.data.label, '@lang("report.bookingOnDate") : '+booking_date);
                  }
            });
        });

        $('body').on('click', '#paymentPerDayButton', function() {
            var payment_date = $('#payment_date').val();
            if(hidden_payment_date==='')
            {
                $('#payment').focus();
                return toastr.error('@lang("report.enterPaymentDate")');
            }
            $.ajax({
                  url: "{{ route('admin.reports.paymentPerDayChart') }}",
                  method: 'post',
                  data: {payment_date:hidden_payment_date, _token:"{{ csrf_token() }}"  },
                  success: function(result)
                  {
                    pie_chart_config(result.data.data, result.data.label, '@lang("report.paymentOnDate") : '+ payment_date);
                  }
            });
        });

        $('body').on('click', '#bookingPerMonthButton', function() {
            var booking_month = $('#booking_month').val();
            if(booking_month==='')
            {
                $('#booking_month').focus();
                return toastr.error('@lang("report.enterBookingMonth")');
            }
            $.ajax({
                  url: "{{ route('admin.reports.bookingPerMonthChart') }}",
                  method: 'post',
                  data: { booking_month:booking_month, _token:"{{ csrf_token() }}"  },
                  success: function(result)
                  {
                    bar_chart_config(result.data.data, result.data.label, '@lang("report.bookingOnMonth") : '+ booking_month);
                  }
            });
        });

        $('body').on('click', '#bookingPerYearButton', function() {
            var booking_year = $('#booking_year').val();
            if(booking_year==='')
            {
                $('#booking_year').focus();
                return toastr.error('@lang("report.enterBookingYear")');
            }
            $.ajax({
                  url: "{{ route('admin.reports.bookingPerYearChart') }}",
                  method: 'post',
                  data: { booking_year:booking_year, _token:"{{ csrf_token() }}"  },
                  success: function(result)
                  {
                      bar_chart_config(result.data.data, result.data.label, '@lang("report.bookingOnYear") : '+ booking_year);
                  }
            });
        });

        $('body').on('click', '#paymentPerMonthButton', function() {
            var payment_month = $('#payment_month').val();
            if(payment_month==='') {
                $('#payment_month').focus();
                return toastr.error('@lang("report.enterPaymentMonth")');

            }
            $.ajax({
                  url: "{{ route('admin.reports.paymentPerMonthChart') }}",
                  method: 'post',
                  data: { payment_month:payment_month, _token:"{{ csrf_token() }}"  },
                  success: function(result)
                  {
                    bar_chart_config(result.data.data, result.data.label, '@lang("report.paymentOnMonth") : '+ payment_month);
                  }
            });
        });

        $('body').on('click', '#paymentPerYearButton', function() {
            var payment_year = $('#payment_year').val();
            if(payment_year==='')
            {
                $('#payment_year').focus();
                return toastr.error('@lang("report.enterPaymentYear")');
            }
            $.ajax({
                  url: "{{ route('admin.reports.paymentPerYearChart') }}",
                  method: 'post',
                  data: { payment_year:payment_year, _token:"{{ csrf_token() }}"  },
                  success: function(result)
                  {
                      bar_chart_config(result.data.data, result.data.label, '@lang("report.bookingOnYear") : '+ payment_year);
                  }
            });
        });

        function pie_chart_config(data, label, text) {
            if(jQuery.isEmptyObject(data) || jQuery.isEmptyObject(label)) {
                $('#canvas-holder').html('<center><h2>@lang("report.noDataFound")...!</h2></center>');
                return true;
            }

            var config = {
                type: 'pie',
                data: {
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            window.chartColors.red,
                            window.chartColors.orange,
                            window.chartColors.blue,
                            window.chartColors.yellow,
                            window.chartColors.green,
                        ],
                    }],
                    labels: label
                },
                options: {
                    responsive: true,
                    showAllTooltips: true,
                    legend: {
                        display: true,
                        position: 'bottom',

                    },
                    title: {
                        display: true,
                        fontsize: 30,
                        text: text
                    },
                }
            };
            $('#chart-area').remove();
            $('#canvas-holder').html('<canvas id="chart-area"></canvas>');
            var ctx = document.getElementById('chart-area').getContext('2d');
            chart = new Chart(ctx, config);
        }

        function bar_chart_config(data, label, text) {
            if(jQuery.isEmptyObject(data)) {
                $('#canvas-holder').html('<center><h2>@lang("report.noDataFound")...!</h2></center>');
                return true;
            }
            var MONTHS = label;
		    var color = Chart.helpers.color;
		    var barChartData = {
                labels: MONTHS,
                datasets: [{
                    label: 'data',
                    backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.red,
                    borderWidth: 1,
                    data: data
                }]

            };

            $('#chart-area').remove();
            $('#canvas-holder').html('<canvas id="chart-area"></canvas>');
			var ctx = document.getElementById('chart-area').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: text
					},
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                userCallback: function(label, index, labels)
                                {
                                    if (Math.floor(label) === label)
                                    {
                                        return label;
                                    }
                                },
                            }
                        }]
                    }
				},
			});
        }

	</script>
@endpush
