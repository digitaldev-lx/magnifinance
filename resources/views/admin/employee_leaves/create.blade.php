<style>
    .select2-container--default .select2-selection--single {
        height: 31px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2.4 !important;
    }

    .d-none {
        display: none;
    }

    .select2 {
        width: 100%;
    }

    #half_day_label {
        margin-top: .2em;
    }

</style>

<div class="modal-header">
    @if ($user->is_employee)
        <h4 class="modal-title">@lang('app.applyLeave')</h4>
    @else
        <h4 class="modal-title">@lang('app.assignLeave')</h4>
    @endif
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form role="form" id="createLeaveForm" class="ajax-form" method="POST" autocomplete="off">
        @csrf
        <div class="row">

            @if ($user->is_admin)
                <div class="col-md-12">
                    <div class="form-group" id="emp">
                        <label>@lang('app.selectEmployee')</label>
                        <select name="employee" id="employee" class="form-control form-control-lg select2">
                            <option value="">@lang('app.select') @lang('app.employee')</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <div class="col-md-12">
                <label for="">@lang('app.startDate')</label>
                <div class="input-group form-group">
                    <input type="text" class="form-control" name="startdate" id="startdate" value="">
                    <span class="input-group-append input-group-addon">
                        <button type="button" class="btn btn-info"><span class="fa fa-calendar-o"></span></button>
                    </span>
                </div>
            </div>
            <div class="col-md-12">
                <label for="">@lang('app.endDate')</label>
                <div class="input-group form-group">
                    <input type="text" class="form-control" name="enddate" id="enddate" value="">
                    <span class="input-group-append input-group-addon">
                        <button type="button" class="btn btn-info"><span class="fa fa-calendar-o"></span></button>
                    </span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">@lang('app.halfday')</label>
                    <br>
                    <label class="switch" id="half_day_label">
                        <input type="checkbox" name="half_day" id="half_day" value="">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="w-100 leavetime d-none">
                <div class="col-md-12">
                    <!-- text input -->
                    <div class="form-group">
                        <label>@lang('app.fromTime')</label>
                        <input type="text" class="form-control" id="starttime" name="starttime" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-12">
                    <!-- text input -->
                    <div class="form-group">
                        <label>@lang('app.toTime')</label>
                        <input type="text" class="form-control" id="endtime" name="endtime" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <label for="">@lang('modules.leaves.reason')</label>
                <div class="form-group">
                    <textarea name="reason" id="reason" class="form-control" cols="30" rows="5"></textarea>
                </div>
            </div>

            <input type="hidden" name="leave_startTime" id="leave_startTime">
            <input type="hidden" name="leave_endTime" id="leave_endTime">

        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>

<script>
    // save leave
    var startDate = '';
    var endDate = '';


    $(document).ready(function() {
        $('body').on('change', '#half_day', function() {
            if ($('#half_day').is(":checked")) {
                $('#half_day').val('true');
                $('.leavetime').removeClass('d-none');
            } else {
                $('#half_day').val('');
                $('.leavetime').addClass('d-none');
                $('#starttime').val('');
                $('#endtime').val('');
                $('#leave_startTime').val('');
                $('#leave_endTime').val('');
            }
        });
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
        startDate = moment(e.date).format('YYYY-MM-DD');
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
        endDate = moment(e.date).format('YYYY-MM-DD');
    });

    function convert(str) {
        var date = new Date(str);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        hours = ("0" + hours).slice(-2);
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }

</script>
