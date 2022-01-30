<style>
    #modal-data-application {
        max-width: 700px;
    }

    .startinput {
        width: 80%;
    }

    .endinput {
        width: 80%;
    }

    .timePicker {
        position: relative;
    }

    .switch {
        margin-top: .2em;
    }

</style>

<div class="modal-header">
    <h4>@lang('app.employee') @lang('app.schedule') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="table-responsive" id="abcd">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th>@lang('app.day')</th>
                    <th>@lang('app.isworking')</th>
                    <th>@lang('app.StartTime')</th>
                    <th>@lang('app.endTime')</th>
                    <th class="text-right">@lang('app.action')</th>
                </tr>
            </thead>
            <tbody id="scheduletable">
                @foreach ($schedule as $schedules)
                    <tr>
                        <td>{{ ucWords($schedules->days) }}</td>
                        <td>
                            <label class="switch" title="Permission required !!">
                                <input type="checkbox" name="isWorking" class="update-working"
                                    id="isWorking{{ $schedules->id }}" data-id="{{ $schedules->id }}"
                                    data-empid="{{ $schedules->employee_id }}" value="yes" @if ($schedules->is_working == 'yes') checked @endif
                                    @if (!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_schedule'))
                                disabled
                @endif>
                <span class="slider round"></span>
                </label>
                </td>
                <td>
                    <div class="timePicker" id="startinputId{{ $schedules->id }}">
                        <span id="startTime-{{ $schedules->id }}">
                            {{ $schedules->is_working == 'yes' ? \Carbon\Carbon::parse($schedules->start_time)->translatedFormat($settings->time_format) : '-------' }}
                        </span>
                    </div>
                    <input type="hidden" id="hiddenstarttime{{ $schedules->id }}"
                        value="{{ \Carbon\Carbon::parse($schedules->start_time)->translatedFormat($settings->time_format) }}">
                </td>
                <td>
                    <div class="timePicker" id="endinputId{{ $schedules->id }}">
                        <span id="endTime-{{ $schedules->id }}">
                            {{ $schedules->is_working == 'yes' ? \Carbon\Carbon::parse($schedules->end_time)->translatedFormat($settings->time_format) : '-------' }}
                        </span>
                    </div>
                    <input type="hidden" id="hiddenendtime{{ $schedules->id }}"
                        value="{{ \Carbon\Carbon::parse($schedules->end_time)->translatedFormat($settings->time_format) }}">
                </td>

                <td id="editButton{{ $schedules->id }}">
                    @if ($schedules->is_working == 'yes')
                        <a href="javascript:;" title="Permission required !!" data-id="{{ $schedules->id }}"
                            data-empid="{{ $schedules->employee_id }}" class="btn btn-primary btn-circle edit-details">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </a>
                    @endif
                </td>
                </tr>
                <input type="hidden" name="schedule_startTime" id="schedule_startTime-{{ $schedules->id }}"
                    value="{{ \Carbon\Carbon::parse($schedules->start_time)->format('h:i a') }}">
                <input type="hidden" name="schedule_endTime" id="schedule_endTime-{{ $schedules->id }}"
                    value="{{ \Carbon\Carbon::parse($schedules->end_time)->format('h:i a') }}">
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
</div>

<script>
    $('body').on('click', '.edit-details', function() {
        let id = $(this).data('id');
        let empid = $(this).data('empid');

        @if (!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_schedule'))
            return false;
        @endif

        var employeeId = empid
        var starttime = $('#startTime-' + id);

        var hiddenstarttime = $('#hiddenstarttime' + id).val();
        var startInput = '<input type="text" class="form-control startinput" id="starttimeinput-' + id +
            '" autocomplete="off" value="' + hiddenstarttime + '">';
        $('#startinputId' + id).html(startInput);

        var hiddenendtime = $('#hiddenendtime' + id).val();
        var endInput = '<input type="text" class="form-control endinput" id="endtimeinput-' + id +
            '" autocomplete="off" value="' + hiddenendtime + '">';
        $('#endinputId' + id).html(endInput);

        var update = '<a href="javascript:;" class="btn btn-success btn-circle update-schedule"  data-id="' +
            id + '" id="updateButton' + id + '" data-empid ="' + employeeId +
            '"><i class="fa fa-check"></i></a>';

        var cancel =
            '<a href="javascript:;" class="btn btn-danger btn-circle cancel-schedule" id="cancelButton' + id +
            '" data-id="' + id + '" data-empid ="' + employeeId + '"><i class="fa fa-close"></i></a>';
        var space = '&nbsp;';
        var button = update += space += cancel;

        $('#editButton' + id).html(button);


        $('#starttimeinput-' + id).datetimepicker({
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
            $('#schedule_startTime-' + id).val(convert(e.date));
        });

        $('#endtimeinput-' + id).datetimepicker({
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
            $('#schedule_endTime-' + id).val(convert(e.date));
        });

    });

    $('body').on('click', '.update-schedule', function() {

        let id = $(this).data('id');
        let empid = $(this).data('empid');

        var starttime = $('#starttimeinput-' + id).val();
        var endtime = $('#endtimeinput-' + id).val();

        if (starttime.length == 0) {
            return toastr.error("@lang('app.StartTime') @lang('errors.fieldRequired')");
        } else if (endtime.length == 0) {
            return toastr.error("@lang('app.endTime') @lang('errors.fieldRequired')");
        }

        var updateId = id;
        var updateStartTime = $('#schedule_startTime-' + updateId).val();
        var updateEndTime = $('#schedule_endTime-' + updateId).val();

        var url = "{{ route('admin.employee-schedule.update', ':updateId') }}";
        url = url.replace(':updateId', updateId);
        $.easyAjax({
            type: 'PUT',
            url: url,
            data: {
                updateStartTime: updateStartTime,
                empid: empid,
                updateEndTime: updateEndTime,
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#scheduletable').html(response.html);
            }
        });
    });

    $('body').on('click', '.cancel-schedule', function() {
        let id = $(this).data('id');
        let empid = $(this).data('empid');
        var start = $('#starttimeinput-' + id);
        var starttime = $('#hiddenstarttime' + id).val();
        var input = $('<span id="startTime-' + id + '">' + starttime + '</span>').val(start.text());
        start.replaceWith(input);

        var end = $('#endtimeinput-' + id);
        var endtime = $('#hiddenendtime' + id).val();
        var input = $('<span id="endTime-' + id + '">' + endtime + '</span>').val(end.text());
        end.replaceWith(input);

        var $updateButton = $('#updateButton' + id);

        var $input = $('<a href="javascript:;" class="btn btn-primary btn-circle edit-details" data-id="' + id +
            '" data-empid="' + empid + '" id="editButton' + id +
            '"><i class="fa fa-pencil" aria-hidden="true"></i></a>').val($updateButton.text());

        $updateButton.replaceWith($input);
        $('#cancelButton' + id).hide();
    });

    $('body').on('click', '.update-working', function() {
        let id = $(this).data('id');
        let empid = $(this).data('empid');

        let isWorking = $('#isWorking' + id).is(':checked') ? 'yes' : 'no';

        let url = "{{ route('admin.updateWorking', ':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                isWorking: isWorking,
                empid: empid,
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#scheduletable').html(response.html);
            }
        });
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
