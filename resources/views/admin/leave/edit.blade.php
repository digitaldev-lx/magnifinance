@extends('layouts.master')

@push('head-css')
    <style>
        .select2-container--default .select2-selection--single{height: 31px !important;}
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height: 2.4 !important;}
        .select2 {
            width: 100%;
        }
        .switch {
            margin-top: .2em;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <div class="card-header">
                        <h3 class="card-title">@lang('app.edit') @lang('app.leave')</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <form role="form" id="editLeaveForm"  class="ajax-form" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $leave->id }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.selectEmployee')</label>
                                    <select name="employee" id="employee" class="form-control form-control-lg select2">
                                        <option value="" >@lang('app.select') @lang('app.employee')</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" @if($leave->employee_id == $employee->id ) selected @endif>{{ $employee->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="">@lang('app.startDate')</label>
                                <div class="input-group form-group">
                                <input type="text" class="form-control" name="startdate" id="startdate" value="{{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat($settings->date_format)}}">
                                    <span class="input-group-append input-group-addon">
                                        <button type="button" class="btn btn-info"><span class="fa fa-calendar-o"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="">@lang('app.endDate')</label>
                                <div class="input-group form-group">

                                    <input type="text" class="form-control" name="enddate" id="enddate" value="{{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat($settings->date_format)}}">
                                    <span class="input-group-append input-group-addon">
                                        <button type="button" class="btn btn-info"><span class="fa fa-calendar-o"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.fullday')</label>
                                    <br>
                                    <label class="switch">
                                        <input type="checkbox" name="full_day" id="full_day" value="true" data-id="{{$leave->id}}"  @if($leave->leave_type == 'Full day') checked @endif)>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        <div class="w-100 leavetoggle" id="toggle-{{$leave->id}}">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.StartTime')</label>
                                <input type="text" class="form-control" id="starttime" name="starttime" value="{{\Carbon\Carbon::parse($leave->start_time)->translatedFormat($settings->time_format)}}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.endTime')</label>
                                    <input type="text" class="form-control" id="endtime"  name="endtime" value="{{\Carbon\Carbon::parse($leave->end_time)->translatedFormat($settings->time_format)}}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                    class="fa fa-check"></i> @lang('app.save')</button>
                        </div>
                        <input type="hidden" name="leave_startTime" id="leave_startTime" value="{{ \Carbon\Carbon::parse($leave->start_time)->format('h:i a')}}">
                        <input type="hidden" name="leave_endTime" id="leave_endTime" value="{{ \Carbon\Carbon::parse($leave->end_time)->format('h:i a')}}">
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-js')
<script>

    var startDate = '{{  \Carbon\Carbon::parse($leave->start_date)->format("Y-m-d") }}';
    var endDate = '{{  \Carbon\Carbon::parse($leave->end_date)->format("Y-m-d") }}';

    $('body').on('click', '#save-form', function() {
        const form = $('#editLeaveForm');

        $.easyAjax({
            url: '{{route('admin.employee-leaves.update', $leave->id)}}',
            container: '#editLeaveForm',
            type: "PUT",
            redirect: true,
            data: form.serialize()+'&startDate='+startDate+'&endDate='+endDate,
            success: function (response) {
                if(response.status == 'success'){
                    window.location.href = '{{ route('admin.employee-leaves.index') }}'
                }
            }
        })
    });

    $(document).ready(function(){
        if($('#full_day').is(":checked")) {
            $('#starttime').val('');
            $('#endtime').val('');
            $('#leave_startTime').val('');
            $('#leave_endTime').val('');
            $('.leavetoggle').hide();
        }
    });

    $('#starttime').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
    }).on('dp.change', function(e) {
        $('#leave_startTime').val(convert(e.date));
    });

    $('#endtime').datetimepicker({
        format: '{{ $time_picker_format }}',
        locale: '{{ $settings->locale }}',
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down",
            previous: "fa fa-angle-double-left",
            next: "fa fa-angle-double-right",
        },
        useCurrent: false,
    }).on('dp.change', function(e) {
        $('#leave_endTime').val(convert(e.date));
    });

    $('#startdate').datetimepicker({
            format: '{{ $date_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right"
            },
            useCurrent: false,
    }).on('dp.change', function(e) {
        startDate =  moment(e.date).format('YYYY-MM-DD');
    });

    $('#enddate').datetimepicker({
        format: '{{ $date_picker_format }}',
        locale: '{{ $settings->locale }}',
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down",
            previous: "fa fa-angle-double-left",
            next: "fa fa-angle-double-right"
        },
        useCurrent: false,
    }).on('dp.change', function(e) {
        endDate =  moment(e.date).format('YYYY-MM-DD');
    });

    function convert(str) {
        var date = new Date(str);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        hours = ("0" + hours).slice(-2);
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }

    // change the selector to use a class
    $('body').on('click', '#full_day', function() {
        $('#starttime').val('');
        $('#endtime').val('');
        $('#leave_startTime').val('');
        $('#leave_endTime').val('');
        // this will query for the clicked toggle
        var $toggle = $(this);

        // build the target form id
        var id = "#toggle-" + $toggle.data('id');

        $(id).toggle();
    });

</script>
@endpush
