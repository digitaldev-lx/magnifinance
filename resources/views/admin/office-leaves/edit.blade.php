<style>
    .select2-container--default .select2-selection--single {
        height: 31px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2.4 !important;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title">@lang('app.edit') @lang('menu.officeleaves')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
        <div class="modal-body">
            <form role="form" id="edit_officeLeave" class="ajax-form" method="POST" autocomplete="off">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $office_leave->id }}">
                <div class="row">
                    <div class="col-md-12">
                        <label for="">@lang('app.title')</label>
                        <div class="form-group">
                        <textarea name="title" id="title" class="form-control" cols="30" rows="5">{{$office_leave->title}}</textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label for="">@lang('app.startDate')</label>
                        <div class="input-group form-group">
                            <input type="text" class="form-control" name="startDate" id="startdate"
                                value="{{ \Carbon\Carbon::parse($office_leave->start_date)->translatedFormat($settings->date_format) }}">
                            <span class="input-group-append input-group-addon">
                                <button type="button" class="btn btn-info"><span
                                        class="fa fa-calendar-o"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="">@lang('app.endDate')</label>
                        <div class="input-group form-group">
                            <input type="text" class="form-control" name="endDate" id="enddate"
                                value="{{ \Carbon\Carbon::parse($office_leave->end_date)->translatedFormat($settings->date_format) }}">
                            <span class="input-group-append input-group-addon">
                                <button type="button" class="btn btn-info"><span
                                        class="fa fa-calendar-o"></span></button>
                            </span>
                        </div>
                    </div>
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
    var startDate = '{{ \Carbon\Carbon::parse($office_leave->start_date)->format('Y-m-d') }}';
    var endDate = '{{ \Carbon\Carbon::parse($office_leave->end_date)->format('Y-m-d') }}';

    $('#save-form').click(function() {
        const form = $('#edit_officeLeave');

        $.easyAjax({
            url: '{{ route('admin.office-leaves.update', $office_leave->id) }}',
            container: '#edit_officeLeave',
            type: "PUT",
            redirect: true,
            data: form.serialize() + '&startDate=' + startDate + '&endDate=' + endDate,
            success: function(response) {
                if (response.status == 'success') {
                    if(response.status == 'success'){
                        window.location.reload();
                    }
                }
            }
        })
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

</script>

