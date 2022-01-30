<style>
    #leave_type {
        background-color: rgb(153, 195, 206);
        border-radius: 10%;
        padding-left: 1%;
        padding-right: 1%;
    }
</style>

<div class="modal-header">
    <h4 class="modal-title">@lang('app.leaveDetail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="col-md-12">
        <div class="row">

            @if ($user->is_admin)
                <div class="col-md-6">
                    <div class="form-group">
                        <h6>@lang('modules.leaves.applicantName')</h6>
                        <p>{{ ucwords($leave->employee->name) }}</p>
                    </div>
                </div>
            @endif

            <div class="col-md-6">
                <div class="form-group">
                    <h6>@lang('modules.leaves.type')</h6>
                    <label class="label" id="leave_type">{{ ucwords($leave->leave_type) }}</label>
                </div>
            </div>
        </div><br>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    @if ($leave->start_date == $leave->end_date)
                        <h6>@lang('app.forDate')</h6>
                    @else
                        <h6>@lang('app.startDate')</h6>
                    @endif
                    <p>
                        <label class="">{{ \Carbon\Carbon::parse($leave->start_date)->format('d-M-Y') }}</label>
                    </p>
                </div>
            </div>

            @if (!is_null($leave->end_date) && $leave->start_date != $leave->end_date)
                <div class="col-md-6">
                    <div class="form-group">
                        <h6>@lang('app.endDate')</h6>
                        <p>
                            <label class="">{{ \Carbon\Carbon::parse($leave->end_date)->format('d-M-Y') }}</label>
                        </p>
                    </div>
                </div>
            @endif
        </div><br>

        <div class="row">
            @if ($leave->leave_type == 'Half day')
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <h6>@lang('app.fromTime')</h6>
                        <label>{{ \Carbon\Carbon::parse($leave->start_time)->translatedFormat($settings->time_format) }}</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <h6>@lang('app.toTime')</h6>
                        <label>{{ \Carbon\Carbon::parse($leave->end_time)->translatedFormat($settings->time_format) }}</label>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="form-group">
                    <h6>@lang('modules.leaves.reason')</h6>
                    <p>{!! $leave->reason !!}</p>
                </div>
            </div>
        </div><br>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <h6>@lang('app.status')</h6>
                    <p>
                        @if ($leave->status == 'approved')
                            <strong class="text-success">@lang('app.approved')</strong>
                        @elseif($leave->status == 'pending')
                            <strong class="text-warning">@lang('app.pending')</strong>
                        @else
                            <strong class="text-danger">@lang('app.rejected')</strong>
                        @endif
                    </p>
                </div>
            </div>

            @if ($leave->status == 'approved' || $leave->status == 'rejected')
                <div class="col-md-6">
                    <div class="form-group">
                        @if ($leave->status == 'approved')
                            <h6>@lang('modules.leaves.approvedBy')</h6>
                        @elseif($leave->status == 'rejected')
                            <h6>@lang('modules.leaves.rejectedBy')</h6>
                        @endif
                        <p>{{ ucwords($leave->approved_by) }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
</div>
