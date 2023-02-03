@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.css') }}">
    <style>
        .chart_menu_li {
            margin: 0 0 7px 0;
        }
        .chart_menu {
            margin-bottom: 14px;
            list-style: none;
            margin-left: -40px;
        }
        .chart_menu li a {
            display: block;
            margin: 0 0 7px 0;
            background: #F7F5F2 ;
            font-size: 12px;
            color: #333;
            padding: 7px 10px 7px 12px;
            text-decoration: none;
        }
        .chart_menu li a:hover{ background: #EFEFEF; }
        .orange{ border-left: 5px solid #fd7e14; }
        .yellow{ border-left: 5px solid #ffc107; }
        .green{ border-left: 5px solid #28a745; }
        .teal{ border-left: 5px solid #20c997; }
        .cyan{ border-left: 5px solid #17a2b8; }
        .white{ border-left: 5px solid #ffffff; }
        .gray{ border-left: 5px solid #6c757d; }
        .primary{ border-left: 5px solid #007bff; }
        .secondary{ border-left: 5px solid #6c757d; }
        .success{ border-left: 5px solid #28a745; }
        .info{ border-left: 5px solid #17a2b8; }
        .warning{ border-left: 5px solid #ffc107; }
        .danger{ border-left: 5px solid #dc3545; }
        .light{ border-left: 5px solid #f8f9fa; }
        .blue { border-left: 5px solid #007bff; }
        .indigo { border-left: 5px solid #6610f2; }
        .purple { border-left:5px solid  #6f42c1; }
        .pink { border-left: 5px solid #e83e8c; }
        .red { border-left: 5px solid #dc3545; }
        .canvas-class {
            height: 400px !important;
        }
    </style>
@endpush

@section('content')
    <ul class="nav nav-tabs mb-5" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="commission-tab" data-toggle="tab" href="#commission" role="tab" aria-controls="commission"
                aria-selected="true">@lang('menu.commissionRevenue')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="customers-tab" data-toggle="tab" href="#customers" role="tab" aria-controls="customers"
                aria-selected="false">@lang('menu.customerReport')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="customers-tab" data-toggle="tab" href="#toutes" role="tab" aria-controls="toutes"
               aria-selected="false">@lang('menu.toutesReport')</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="commission" role="tabpanel" aria-labelledby="commission-tab">@include('superadmin.report.commission')</div>
        <div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="customers-tab">@include('superadmin.report.customer-report')</div>
        <div class="tab-pane fade" id="toutes" role="tabpanel" aria-labelledby="toutes-tab">@include('superadmin.report.toutes')</div>
    </div>
@endsection

@push('footer-js')
    <script src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}"></script>
    @if ($settings->locale !== 'en')
        <script src="{{ 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.'.$settings->locale.'.min.js' }}" charset="UTF-8"></script>
    @endif
    <script>
        const renderTable = (tableId, url, data, columns=[]) => {
            $("#"+tableId).dataTable().fnDestroy();
            const table = $("#"+tableId).dataTable({
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csvHtml5', text: '@lang("app.exportCSV")' }
                ],
                responsive: true,
                serverSide: true,
                ajax: {'url' : url,
                    "data": function ( d ) {
                        return $.extend( {}, d, data );
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
                    ...columns
                ]
            });
            new $.fn.dataTable.FixedHeader( table );
        }

        const generateChart = (labels, data, chartId, label,type='bar') => {
            const ctx = document.getElementById(chartId).getContext('2d');
            const labelArray = labels;
            const dataArray = data;

            const myChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: [...labelArray],
                    datasets: [{
                        label: label,
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
            });
        }

        const chartRequest = (url, data, chartId, containerId, label,type='bar') => {
            let token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                data: {...data, '_token': token},
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
                        resetCanvas(chartId, containerId);
                        generateChart(response.labels, response.data, chartId, label,type);
                    }
                }
            });
        }

        const resetCanvas = (chartId, containerId) => {
            $('#'+chartId).remove(); // this is my <canvas> element
            $('#'+containerId).append('<canvas id="'+chartId+'" class="canvas-class"><canvas>');
            canvas = document.querySelector('#'+chartId);
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
