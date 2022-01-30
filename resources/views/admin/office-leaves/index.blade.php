<div class="table-responsive">
    <div class="d-flex justify-content-center justify-content-md-end mb-3">
        <a href="javascript:;" id="create-office-leaves" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
    </div>

    <table id="OfficeLeaveTable" class="table w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('app.title')</th>
                <th>@lang('app.startDate')</th>
                <th>@lang('app.endDate')
                <th class="text-right">@lang('app.action')</th>
            </tr>
            @foreach($officeLeaves as $key => $officeLeave)
                 <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $officeLeave->title }}</td>
                    <td>{{ $officeLeave->start_date }}</td>
                    <td>{{ $officeLeave->end_date }}</td>
                    <td class="text-right">
                        <a href="javascript:;" data-row-id="{{ $officeLeave->id }}"
                            class="btn btn-primary btn-rounded btn-sm edit-officeLeave"><i
                                class="icon-pencil"></i> @lang('app.edit')</a>
                        <a href="javascript:;" data-row-id="{{ $officeLeave->id }}"
                            class="btn btn-danger btn-rounded btn-sm delete-officeLeave"><i
                            class="icon-pencil"></i> @lang('app.delete')</a>
                    </td>
                </tr>
            @endforeach
        </thead>
    </table>
</div>
