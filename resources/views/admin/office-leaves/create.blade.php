<style>
    .select2-container--default .select2-selection--single {
        height: 31px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2.4 !important;
    }

</style>
<div class="modal-header">
    <h4 class="modal-title">@lang('app.create') @lang('menu.officeleaves')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
            <div class="modal-body">
                <form role="form" id="createOfficeLeaveForm" class="ajax-form" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">@lang('app.title')</label>
                            <div class="form-group">
                                <textarea name="title" id="title" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="startDate">@lang('app.startDate')</label>
                            <div class="input-group form-group">
                                <input type="text" class="form-control" name="startDate" id="startdate" value="" autocomplete="off">
                                <span class="input-group-append input-group-addon">
                                    <button type="button" class="btn btn-info"><span
                                            class="fa fa-calendar-o"></span></button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="endDate">@lang('app.endDate')</label>
                            <div class="input-group form-group">
                                <input type="text" class="form-control" name="endDate" id="enddate" value="" autocomplete="off">
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


    $(document).ready(function() {

        var startDate =  '';
        var endDate = '' ;
        // save officeleave
        $('body').on('click', '#save-form', function() {
            const form = $('#createOfficeLeaveForm');
            $.easyAjax({
                url: '{{route('admin.office-leaves.store')}}',
                container: '#createOfficeLeaveForm',
                type: "POST",
                redirect: true,
                data: form.serialize()+'&startDate='+startDate+'&endDate='+endDate,
                success: function (response) {
                    if(response.status == 'success'){
                        window.location.reload();
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
    });

</script>

