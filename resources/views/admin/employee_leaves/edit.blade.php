<style>
    .switch {
        margin-top: .2em;
    }

    .d-none {
        display: none;
    }

</style>

<div class="modal-header">
    <h4 class="modal-title">@lang('app.editLeave')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <form role="form" id="editLeaveForm" class="ajax-form" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $leave->id }}">
        <div class="row">

            <div class="col-md-12">
                <label for="">@lang('app.startDate')</label>
                <div class="input-group form-group">
                    <input type="text" class="form-control" name="startdate" id="startdate"
                        value="{{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat($settings->date_format) }}">
                    <span class="input-group-append input-group-addon">
                        <button type="button" class="btn btn-info"><span class="fa fa-calendar-o"></span></button>
                    </span>
                </div>
            </div>

            <div class="col-md-12">
                <label for="">@lang('app.endDate')</label>
                <div class="input-group form-group">

                    <input type="text" class="form-control" name="enddate" id="enddate"
                        value="{{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat($settings->date_format) }}">
                    <span class="input-group-append input-group-addon">
                        <button type="button" class="btn btn-info"><span class="fa fa-calendar-o"></span></button>
                    </span>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">@lang('app.halfday')</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="half_day" id="half_day" value="" data-id="{{ $leave->id }}" @if ($leave->leave_type == 'Half day') checked @endif)>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>

            <div class="w-100 leavetoggle @if ($leave->leave_type == 'Full day') d-none @endif" id="toggle-{{ $leave->id }}">
                <div class="col-md-12">
                    <!-- text input -->
                    <div class="form-group">
                        <label>@lang('app.fromTime')</label>
                        <input type="text" class="form-control" id="starttime" name="starttime"
                            value="{{ \Carbon\Carbon::parse($leave->start_time)->translatedFormat($settings->time_format) }}"
                            autocomplete="off">
                    </div>
                </div>

                <div class="col-md-12">
                    <!-- text input -->
                    <div class="form-group">
                        <label>@lang('app.toTime')</label>
                        <input type="text" class="form-control" id="endtime" name="endtime"
                            value="{{ \Carbon\Carbon::parse($leave->end_time)->translatedFormat($settings->time_format) }}"
                            autocomplete="off">
                    </div>
                </div>

            </div>

            <div class="col-md-12">
                <label for="">@lang('app.reason')</label>
                <div class="form-group">
                    <textarea name="reason" id="reason" class="form-control" cols="30"
                        rows="5">{{ $leave->reason }}</textarea>
                </div>
            </div>

            <input type="hidden" name="leave_startTime" id="leave_startTime"
                value="{{ \Carbon\Carbon::parse($leave->start_time)->format('h:i a') }}">
            <input type="hidden" name="leave_endTime" id="leave_endTime"
                value="{{ \Carbon\Carbon::parse($leave->end_time)->format('h:i a') }}">

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
    var startDate = "{{ \Carbon\Carbon::parse($leave->start_date)->format('Y-m-d') }}";
    var endDate = "{{ \Carbon\Carbon::parse($leave->end_date)->format('Y-m-d') }}";

    $('body').on('click', '#save-form', function() {
        const form = $('#editLeaveForm');

        $.easyAjax({
            url: '{{ route('admin.employee-leaves.update', $leave->id) }}',
            container: '#editLeaveForm',
            type: "PUT",
            redirect: true,
            data: form.serialize() + '&startDate=' + startDate + '&endDate=' + endDate,
            success: function(response) {
                if (response.status == 'success') {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            }
        })
    });

    $(document).ready(function() {
        $('body').on('change', '#half_day', function() {
            if ($('#half_day').is(":checked")) {
                $('#half_day').val('true');
                $('#starttime').val('');
                $('#endtime').val('');
                $('#leave_startTime').val('');
                $('#leave_endTime').val('');

                var $toggle = $(this);
                var id = "#toggle-" + $toggle.data('id');
                $(id).show();
            } else {
                var $toggle = $(this);
                var id = "#toggle-" + $toggle.data('id');
                $(id).hide();

                $('#half_day').val('');
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
    };

</script>
