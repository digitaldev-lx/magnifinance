@extends('layouts.master')

@push('head-css')
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
<style>
        .link-stats {
        cursor: pointer;
    }

    .canvas-class {
        height: 400px !important;
    }
</style>
@endpush

@section('content')

<div class="row mb-2">
    @if($user->is_superadmin)

    @if ($isNotSetExchangeRate)
    <div class="col-md-12">
        <div class="alert alert-warning">
                <div class="row">
                    <div class="col-md-10 d-flex align-items-center">@lang('messages.exchangeRateNotSet')
                    </div>
                    <div class="col-md-2 text-right">
                        <a href="{{ route('superadmin.settings.index').'#currency' }}" ><button type="button" role="button" class="btn btn-success">@lang('app.update')</button> </a>
                    </div>
                </div>
        </div>
    </div>
    @endif
    @if ($settings->website != url('/'))
    <div class="col-md-12">
        <div class="alert alert-warning">
                <div class="row">
                    <div class="col-md-10 d-flex align-items-center">@lang('messages.setDomain') &nbsp; <strong> {{url('/')}}</strong>
                    </div>
                    <div class="col-md-2 text-right">
                        <a href="{{ route('superadmin.settings.index') }}" ><button type="button" role="button" class="btn btn-success">@lang('app.update')</button> </a>
                    </div>
                </div>
        </div>
    </div>
    @endif
    @endif
    @if (!$user->mobile_verified && $smsSettings->nexmo_status == 'active')
    <div id="verify-mobile-info" class="col-md-12">
        <div class="alert alert-info col-md-12" role="alert">
            <div class="row">
                <div class="col-md-10 d-flex align-items-center">
                    <i class="fa fa-info fa-3x mr-2"></i>
                    @lang('messages.info.verifyAlert')
                </div>
                <div class="col-md-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('superadmin.profile.index') }}" class="btn btn-warning">
                        @lang('menu.profile')
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (strlen($smsSettings->nexmo_from) > 18)
    <div id="brand-length" class="col-md-12">
        <div class="alert alert-danger col-md-12" role="alert">
            <div class="row">
                <div class="col-md-10 d-flex align-items-center">
                    <i class="fa fa-exclamation-triangle fa-3x mr-2"></i>
                    @lang('messages.info.smsNameAlert')
                </div>
                <div class="col-md-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('superadmin.settings.index').'#sms-settings' }}" class="btn btn-info">
                        @lang('menu.settings')
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-md-12">
        <h6>@lang('app.dateRange')</h6>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <input type="text" class="form-control datepicker" name="start_date" id="start-date"
                   placeholder="@lang('app.startDate')"
                   value="{{ \Carbon\Carbon::today()->subDays(30)->format($settings->date_format) }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <input type="text" class="form-control datepicker" name="end_date" id="end-date"
                   placeholder="@lang('app.endDate')" value="{{ \Carbon\Carbon::today()->format($settings->date_format) }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <button type="button" id="apply-filter" class="btn btn-success">
                <i class="fa fa-check"></i> @lang('app.apply')
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="row">
            {{-- total companies --}}
            {{-- <div class="col-xl-4 col-sm-6 ">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="nav-icon icon-home"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('app.totalCompanies')</span>
                        <span class="info-box-number" id="total-companies">0</span>
                    </div>
                </div>
            </div> --}}
            {{-- total companies --}}
            {{-- active companies --}}
            <div class="col-xl-4 col-sm-6 ">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="nav-icon icon-home"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('modules.dashboard.ActiveCompanies')</span>
                        <span class="info-box-number" id="total-active-companies">0</span>
                    </div>
                </div>
            </div>
            {{-- active companies --}}
            {{-- inactive companies --}}
            <div class="col-xl-4 col-sm-6 ">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="nav-icon icon-home"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('modules.dashboard.InActiveCompanies')</span>
                        <span class="info-box-number" id="total-inactive-companies">0</span>
                    </div>
                </div>
            </div>
            {{-- inactive companies --}}

            <div class="col-xl-4 col-sm-6 ">
                <div class="info-box">
                    <span class="info-box-icon bg-dark-gradient"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('modules.dashboard.totalCustomers')</span>
                        <span class="info-box-number" id="total-customers">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

             <div class="col-xl-4 col-sm-6 ">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fa fa-th-large"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('modules.dashboard.totalCategories')</span>
                        <span class="info-box-number" id="total-categories">{{$totalCategories}}</span>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-xl-4 col-sm-6 ">
                <div class="info-box">
                    <span class="info-box-icon bg-success">{{ $settings->currency->currency_symbol }}</span>
                    <div class="info-box-content">
                        <span class="info-box-text">@lang('modules.dashboard.totalEarning')</span>
                        <span class="info-box-number" id="total-earning">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <!-- /.col -->
            <div class="col-xl-4 col-sm-6 ">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fa fa-bullhorn"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{__('modules.dashboard.totalInToutes')}}</span>
                        <span class="info-box-number" id="total-toutes">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 row p-0 m-0">
                <div class="col-lg-6 ">
                    <div class="info-box">
                    <div class="info-box-content">
                    <h6>@lang('menu.earningReport')</h6>
                    <div id="earning-graph-container">
                        <canvas id="earningChart"></canvas>
                    </div>
                    </div>
                </div>
                </div>
                <div class="col-lg-6 ">
                    <div class="info-box">
                    <div class="info-box-content">
                    <h6>@lang('menu.salesReport')</h6>
                    <div id="sales-graph-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-12 row p-0 m-0">
                <div class="col-lg-12 ">
                    <div class="info-box">
                    <div class="info-box-content">
                    <h6>@lang('menu.commissionRevenue')</h6>
                    <div id="commission-revenue-graph-container">
                        <canvas id="commissionRevenueChart"></canvas>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-12 row p-0 m-0">
                <div class="col-lg-12 ">
                    <div class="info-box">
                        <div class="info-box-content">
                            <h6>@lang('menu.toutesRevenue')</h6>
                            <div id="toutes-revenue-graph-container">
                                <canvas id="toutesRevenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 row p-0 m-0">
                <div class="col-lg-6 ">
                    <div class="info-box">
                    <div class="info-box-content">
                    <h6>@lang('menu.newCustomers')</h6>
                    <div id="new-customers-graph-container">
                        <canvas id="newCustomersChart"></canvas>
                    </div>
                    </div>
                </div>
                </div>
                <div class="col-lg-6 ">
                    <div class="info-box">
                    <div class="info-box-content">
                    <h6>@lang('menu.newVendors')</h6>
                    <div id="new-vendors-graph-container">
                        <canvas id="newVendorsChart"></canvas>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12" id="todo-items-list">
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-js')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script>
    var updated = true;

        function showNewTodoForm() {
            let url = "{{ route('superadmin.todo-items.create') }}"
            $(modal_default + ' ' + modal_heading).html('...');
            $.ajaxModal(modal_default, url);
        }

        function initSortable() {
            let updates = {'pending-tasks': false, 'completed-tasks': false};
            let completedFirstPosition = $('#completed-tasks').find('li.draggable').first().data('position');
            let pendingFirstPosition = $('#pending-tasks').find('li.draggable').first().data('position');

            $('#pending-tasks').sortable({
                connectWith: '#completed-tasks',
                cursor: 'move',
                handle: '.handle',
                stop: function (event, ui) {
                    const id = ui.item.data('id');
                    const oldPosition = ui.item.data('position');

                    if (updates['pending-tasks']===true && updates['completed-tasks']===true) {
                        const inverseIndex =  completedFirstPosition > 0 ? completedFirstPosition - ui.item.index() + 1 : 1;
                        const newPosition = inverseIndex;
                        updateTodoItem(id, position={oldPosition, newPosition}, status='completed');
                    } else if(updates['pending-tasks']===true && updates['completed-tasks']===false) {
                        const newPosition = pendingFirstPosition - ui.item.index();
                        updateTodoItem(id, position={oldPosition, newPosition});
                    }

                    //finally, clear out the updates object
                    updates['pending-tasks']=false;
                    updates['completed-tasks']=false;
                },
                update: function (event, ui) {
                    updates[$(this).attr('id')] = true;
                }
            }).disableSelection();

            $('#completed-tasks').sortable({
                connectWith: '#pending-tasks',
                cursor: 'move',
                handle: '.handle',
                stop: function (event, ui) {
                    const id = ui.item.data('id');
                    const oldPosition = ui.item.data('position');

                    if (updates['pending-tasks']===true && updates['completed-tasks']===true) {
                        const inverseIndex =  pendingFirstPosition > 0 ? pendingFirstPosition - ui.item.index() + 1 : 1;
                        const newPosition = inverseIndex;
                        updateTodoItem(id, position={oldPosition, newPosition}, status='pending');
                    } else if(updates['pending-tasks']===false && updates['completed-tasks']===true) {
                        const newPosition = completedFirstPosition - ui.item.index();
                        updateTodoItem(id, position={oldPosition, newPosition});
                    }

                    //finally, clear out the updates object
                    updates['pending-tasks']=false;
                    updates['completed-tasks']=false;
                },
                update: function (event, ui) {
                    updates[$(this).attr('id')] = true;
                }
            }).disableSelection();
        }


        function updateTodoItem(id, pos, status=null) {
            let data = {
                _token: '{{ csrf_token() }}',
                id: id,
                position: pos,
            };

            if (status) {
                data = {...data, status: status}
            }

            $.easyAjax({
                url: "{{ route('superadmin.todo-items.updateTodoItem') }}",
                type: 'POST',
                data: data,
                container: '#todo-items-list',
                success: function (response) {
                    $('#todo-items-list').html(response.view);
                    initSortable();
                }
            });
        }

        function showUpdateTodoForm(id) {
            let url = "{{ route('superadmin.todo-items.edit', ':id') }}"
            url = url.replace(':id', id);
            $(modal_default + ' ' + modal_heading).html('...');
            $.ajaxModal(modal_default, url);
        }

        function deleteTodoItem(id) {
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }).then((willDelete) => {
                if (willDelete) {
                    let url = "{{ route('superadmin.todo-items.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    let data = {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    }

                    $.easyAjax({
                        url,
                        data,
                        type: 'POST',
                        container: '#roleMemberTable',
                        success: function (response) {
                            if (response.status == 'success') {
                                $('#todo-items-list').html(response.view);
                                initSortable();
                            }
                        }
                    })
                }
            });
        }

        $('.datepicker').datetimepicker({
            format: '{{ $date_picker_format }}',
            locale: '{{ $settings->locale }}',
            allowInputToggle: true,
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right'
            }
        })


            $('#todo-items-list').html(`{!! $todoItemsView !!}`);



        $('body').on('click', '#apply-filter', function () {
            calculateStats();
        });


        $('body').on('click', '#create-todo-item', function () {
            $.easyAjax({
                url: "{{route('superadmin.todo-items.store')}}",
                container: '#createTodoItem',
                type: "POST",
                data: $('#createTodoItem').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $('#todo-items-list').html(response.view);
                        initSortable();
                        $(modal_default).modal('hide');
                    }
                }
            })
        });

        $('body').on('click', '#update-todo-item', function () {
            const id = $(this).data('id');
            let url = "{{route('superadmin.todo-items.update', ':id')}}"
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                container: '#editTodoItem',
                type: "POST",
                data: $('#editTodoItem').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $('#todo-items-list').html(response.view);
                        initSortable();

                        $(modal_default).modal('hide');
                    }
                }
            })
        });

        $('body').on('change', '#todo-items-list input[name="status"]', function () {
            const id = $(this).data('id');
            let status = 'pending';

            if ($(this).is(':checked')) {
                status = 'completed';
            }

            updateTodoItem(id, null, status);
        })


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
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
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
            $('#'+containerId).append('<canvas id="'+chartId+'" class="canvas-class"></canvas>');
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


        function earning_callback(startDate,endDate) {
                chartRequest(
                    '{{ route("superadmin.reports.earningReportChart") }}',
                    {
                        startDate: startDate,
                        endDate: endDate,
                    },
                    'earningChart',
                    'earning-graph-container',
                    '@lang("app.amount")',
                    'bar'
                );
        }

        function sales_callback(startDate,endDate) {
                chartRequest(
                    '{{ route("superadmin.reports.salesReportChart") }}',
                    {
                        startDate: startDate,
                        endDate: endDate,
                    },
                    'salesChart',
                    'sales-graph-container',
                    '@lang("app.sales")',
                    'doughnut',
                );
        }
        function newCustomers_callback(startDate,endDate) {
                chartRequest(
                    '{{ route("superadmin.reports.newCustomersChart") }}',
                    {
                        startDate: startDate,
                        endDate: endDate,
                    },
                    'newCustomersChart',
                    'new-customers-graph-container',
                    '@lang("menu.newCustomers")',
                    'line',
                );
        }
        function newVendors_callback(startDate,endDate) {
                chartRequest(
                    '{{ route("superadmin.reports.newVendorsChart") }}',
                    {
                        startDate: startDate,
                        endDate: endDate,
                    },
                    'newVendorsChart',
                    'new-vendors-graph-container',
                    '@lang("menu.newVendors")',
                    'line'
                );
        }
        function commissionRevenue_callback(startDate,endDate) {
                chartRequest(
                    '{{ route("superadmin.reports.commissionRevenueChart") }}',
                    {
                        startDate: startDate,
                        endDate: endDate,
                    },
                    'commissionRevenueChart',
                    'commission-revenue-graph-container',
                    '@lang("menu.commissionRevenue")',
                    'line'
                );
        }
    function toutesRevenue_callback(startDate,endDate) {
        chartRequest(
            '{{ route("superadmin.reports.toutesRevenueChart") }}',
            {
                startDate: startDate,
                endDate: endDate,
            },
            'toutesRevenueChart',
            'toutes-revenue-graph-container',
            '@lang("menu.toutesRevenue")',
            'line'
        );
    }


        function calculateStats() {
            let startDate = $('#start-date').val();
            let endDate = $('#end-date').val();
            earning_callback(startDate,endDate);
            sales_callback(startDate,endDate);
            newCustomers_callback(startDate,endDate);
            newVendors_callback(startDate,endDate);
            commissionRevenue_callback(startDate,endDate);
            toutesRevenue_callback(startDate,endDate);
            $.easyAjax({
                type: 'GET',
                url: '{{ route("superadmin.dashboard") }}',
                data: {startDate: startDate, endDate: endDate},
                success: function (response) {
                    if (response.status == "success") {

                        $.unblockUI();
                        $('#total-active-companies').html(response.activeCompanies);
                        $('#total-inactive-companies').html(response.deActiveCompanies);
                        $('#total-customers').html(response.totalCustomers);
                        $('#total-vendors').html(response.totalVendors);
                        $('#total-earning').html(response.totalEarnings);
                        $('#total-toutes').html(response.totalToutes);
                    }
                }
            });
        }


    calculateStats();
    initSortable();
</script>
@endpush
