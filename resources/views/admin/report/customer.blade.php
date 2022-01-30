@extends('layouts.master')

@push('head-css')
<style>
    #reportrange {
        background: #fff;
        cursor: pointer;
        padding: 15px 20px;
        border: 1px solid #ccc;
        width: 100%
    }
    #myChart {
        height: 400px !important;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-6">
                        <h6>@lang('app.dateRange')</h6>
                        <div id="reportrange" class="form-group">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                            <input type="hidden" id="start-date">
                            <input type="hidden" id="end-date">
                        </div>
                        </div>
                    </div>
                    <!-- Custom Tabs -->
                    <div class="card">
                        <div class="card-header d-flex p-0">
                        <h3 class="card-title p-3">@lang('menu.earningReport')</h3>
                        <ul class="nav nav-pills ml-auto p-2">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.index') }}" >@lang('menu.earningReport')</a></li>
                            <li class="nav-item"><a class="nav-link active" href="{{ route('admin.reports.customer') }}" >@lang("menu.customerReport")</a></li>
                            <li class="nav-item"><a class="nav-link" href="#tab_3" data-toggle="tab">@lang('app.tab') 3</a></li>
                        </ul>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div id="graph-container">
                                    <canvas id="myChart"></canvas>
                                </div>
                                <hr>
                                <div class="table-responsive">
                                    <table id="myTable" class="table ">
                                    <thead>
                                        <tr>
                                            <th>@lang('app.booking') #</th>
                                            <th>@lang('app.customer')</th>
                                            <th>@lang('app.amount')</th>
                                            <th>@lang('app.date')</th>
                                        </tr>
                                    </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- ./card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
        </div>
    </div>
@endsection

@push('footer-js')
  <script>
    $(function() {
      $('input[name="daterange"]').daterangepicker({
        opens: 'left'
      }, function(start, end, label) {
      });

      var start = moment().subtract(90, 'days');
      var end = moment();

      function cb(start, end) {
          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
          $('#start-date').val(start.format('YYYY-MM-D'));
          $('#end-date').val(end.format('YYYY-MM-D'));
          chartRequest();
          earningTable();
      }

      $('#reportrange').daterangepicker({
          startDate: start,
          endDate: end,
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          }
        },
      cb);

      cb(start, end);
    });

    const earningTable = () => {
      $("#myTable").dataTable().fnDestroy();
      const table = $('#myTable').dataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'csvHtml5', text: '@lang("app.exportCSV")' }
        ],
        responsive: true,
        serverSide: true,
        ajax: {'url' : '{!! route('admin.reports.earningTable') !!}',
            "data": function ( d ) {
                return $.extend( {}, d, {
                    "startDate": $('#start-date').val(),
                    "endDate": $('#end-date').val()
                } );
            }
        },
        language: languageOptions(),
        "fnDrawCallback": function( oSettings ) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },
        columns: [
            { data: 'DT_RowIndex'},
            { data: 'user_id', name: 'user_id' },
            { data: 'amount_to_pay', name: 'amount_to_pay' },
            { data: 'date_time', name: 'date_time' },
        ]
      });
      new $.fn.dataTable.FixedHeader( table );

    }

    const generateChart = (labels, data) => {
        const ctx = document.getElementById("myChart").getContext('2d');
        const labelArray = labels;
        const dataArray = data;

        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [...labelArray],
                datasets: [{
                    label: '@lang("app.amount")',
                    data: [...dataArray],
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
          }
        );
    }

    const chartRequest = () => {
      let url  = '{{ route("admin.reports.earningReportChart") }}';
      let startDate = $('#start-date').val();
      let endDate = $('#end-date').val();
      let token = "{{ csrf_token() }}";

      $.easyAjax({
          type: 'POST',
          url: url,
          data: {'_token': token, startDate: startDate, endDate: endDate},
          success: function (response) {
              if (response.status == "success") {
                  $.unblockUI();
                  resetCanvas();
                  generateChart(response.labels, response.earnings);
              }
          }
      });
    }

    const resetCanvas = () => {
      $('#myChart').remove(); // this is my <canvas> element
      $('#graph-container').append('<canvas id="myChart"><canvas>');
      canvas = document.querySelector('#myChart');
      ctx = canvas.getContext('2d');
      ctx.canvas.width = $('#graph').width(); // resize to parent width
      ctx.canvas.height = $('#graph').height(); // resize to parent height
      var x = canvas.width/2;
      var y = canvas.height/2;
      ctx.font = '10pt Verdana';
      ctx.textAlign = 'center';
      ctx.fillText('This text is centered on the canvas', x, y);
    }
  </script>

@endpush
