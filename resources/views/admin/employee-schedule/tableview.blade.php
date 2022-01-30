
@foreach($schedule as $schedules)
    <tr>
    <td>{{$schedules->days}}</td>
    <td>
        <label class="switch">
        <input type="checkbox" name="isWorking" id="isWorking{{$schedules->id}}" data-id="{{$schedules->id}}" data-empid ="{{$schedules->employee_id}}" class="update-working" value="yes" @if($schedules->is_working == 'yes') checked @endif>
            <span class="slider round"></span>
        </label>
    </td>
    <td>
        <div class="timePicker" id="startinputId{{$schedules->id}}">
        <span id="startTime-{{$schedules->id}}">
            {{ ($schedules->is_working == 'yes') ?
            (\Carbon\Carbon::parse($schedules->start_time)->translatedFormat($settings->time_format)) : '-------'}}
        </span>
        </div>
        <input type="hidden" id="hiddenstarttime{{$schedules->id}}" value="{{\Carbon\Carbon::parse($schedules->start_time)->translatedFormat($settings->time_format)}}">
    </td>
    <td>
        <div class="timePicker" id="endinputId{{$schedules->id}}">
        <span id="endTime-{{$schedules->id}}">
            {{ ($schedules->is_working == 'yes') ?
            (\Carbon\Carbon::parse($schedules->end_time)->translatedFormat($settings->time_format)) : '-------'}}
        </span>
        </div>
        <input type="hidden" id="hiddenendtime{{$schedules->id}}" value="{{\Carbon\Carbon::parse($schedules->end_time)->translatedFormat($settings->time_format)}}">
    </td>

    <td id="editButton{{$schedules->id}}">
        @if($schedules->is_working == 'yes')
            <a href="javascript:;" data-id="{{$schedules->id}}"  data-empid ="{{$schedules->employee_id}}" class="btn btn-primary btn-circle edit-details">
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </a>
        @endif
    </td>
    </tr>
    <input type="hidden" name="schedule_startTime" id="schedule_startTime-{{$schedules->id}}" value="{{ \Carbon\Carbon::parse($schedules->start_time)->format('h:i a')}}">
    <input type="hidden" name="schedule_endTime" id="schedule_endTime-{{$schedules->id}}" value="{{ \Carbon\Carbon::parse($schedules->end_time)->format('h:i a')}}">
@endforeach
