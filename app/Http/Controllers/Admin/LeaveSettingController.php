<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Leave\StoreLeave;
use Carbon\Carbon;
use App\User;
use App\Leave;
use App\Notifications\newLeave;
use App\Notifications\updateLeaveStatus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class LeaveSettingController  extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('app.leave'));

        $this->middleware(function ($request, $next) {

            if (!in_array('Employee Leave', $this->user->modules)) {
                abort(403);
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee_leave'), 403);

        if (request()->ajax()) {

            $leave = Leave::where('company_id', $this->user->company->id)->with('employee');

            if (\request('leavetype') != '') {
                $leave->where('leave_type', \request('leavetype'));
            }

            if (\request('leave_status') != '') {
                $leave->where('status', \request('leave_status'));
            }

            if (\request('employee_id') != '') {
                $employee = \request('employee_id');

                $leave = $leave->whereHas('employee', function ($query) use ($employee) {

                    $query->where('id', $employee);
                });
            }

            if (\request('fromdate') && request('todate')) {
                $leave->whereDate('start_date', '>=', \request('fromdate'))->whereDate('end_date', '<=', \request('todate'));
            }

            $leave = $leave->orderBy('id', 'desc')->get();

            return datatables()->of($leave)
                ->addColumn('action', function ($row) {

                    $action = '<div class="text-right">';

                    $action .= '<a href="javascript:;" class="btn btn-info btn-circle view-leave" data-leave-id="' . $row->id . '"
                            data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a>&nbsp';

                    if ($row->status == 'pending') {
                        if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_employee_leave')) {

                            $action .= '<a href="javascript:;" class="btn btn-primary btn-circle edit-leave" data-leave-id="' . $row->id . '"
                                data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                        }

                        if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_employee_leave')) {

                            $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-leave-row"
                                data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                        }
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->employee->name);
                })
                ->editColumn('end_date', function ($row) {

                    if ($row->end_date) {
                        return $row->end_date;
                    }
                    else {
                        return '------';
                    }
                })
                ->editColumn('start_time', function ($row) {

                    if ($row->start_time) {
                        return \Carbon\Carbon::parse($row->start_time)->translatedFormat($this->settings->time_format);
                    }
                    else {
                        return '------';
                    }
                })
                ->editColumn('end_time', function ($row) {
                    if ($row->end_time) {
                        return \Carbon\Carbon::parse($row->end_time)->translatedFormat($this->settings->time_format);
                    }
                    else {
                        return '------';
                    }
                })
                ->addColumn('status', function ($row) {
                    $selected = '';

                    if ($row->status == 'pending') {
                        $selected = 'selected';
                        return '<select class="custom-select status" data-leave-id="' . $row->id . '">
                        <option value="pending" ' . $selected . '>Pending</option>
                        <option value="approved"  >Approved</option>
                        <option value="rejected" >Rejected</option>
                        </select>';
                    }

                    if ($row->status == 'approved') {
                        return '<label class="label label-success approved-label">'
                            . ucfirst($row->status) . '</label>';
                    }
                    elseif ($row->status == 'rejected') {
                        return '<label class="label label-danger rejected-label">'
                            . ucfirst($row->status) . '</label>';
                    }

                })
                ->addIndexColumn()
                ->rawColumns(['status', 'action'])
                ->toJson();
        }

        $employees = User::AllEmployees()->get();
        return view('admin.employee_leaves.index', compact('employees'));
    }

    // fetch leaves section on employee panel
    public function view()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee_leave'), 403);

        $employee = Auth::user()->id;

        if (request()->ajax()) {

            $leave = Leave::whereHas('employee', function ($query) use ($employee) {

                $query->where('id', $employee);
            });

            if (\request('leave_type') != '') {
                $leave->where('leave_type', \request('leave_type'));
            }

            if (\request('leave_status') != '') {
                $leave->where('status', \request('leave_status'));
            }

            if (\request('fromdate') && request('todate')) {
                $leave->whereDate('start_date', '>=', \request('fromdate'))->whereDate('end_date', '<=', \request('todate'));
            }

            $leave = $leave->orderBy('id', 'desc')->get();

            return datatables()->of($leave)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';
                    $action .= '<a href="javascript:;" class="btn btn-info btn-circle view-leave" data-leave-id="' . $row->id . '"
                             data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a>&nbsp';

                    if ($row->status == 'pending') {

                        $action .= '<a href="javascript:;" class="btn btn-primary btn-circle edit-leave" data-leave-id="' . $row->id . '"
                                 data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-leave"
                                 data-toggle="tooltip" data-leave-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('end_date', function ($row) {
                    if ($row->end_date) {
                        return $row->end_date;
                    }
                    else {
                        return '--';
                    }
                })
                ->editColumn('start_time', function ($row) {
                    if ($row->start_time) {
                        return \Carbon\Carbon::parse($row->start_time)->translatedFormat($this->settings->time_format);
                    }
                    else {
                        return '--';
                    }
                })
                ->editColumn('end_time', function ($row) {
                    if ($row->end_time) {
                        return \Carbon\Carbon::parse($row->end_time)->translatedFormat($this->settings->time_format);
                    }
                    else {
                        return '--';
                    }
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'pending') {
                        return '<label class="label label-warning label-pending">'
                            . ucfirst($row->status) . '</label>';
                    }
                    elseif ($row->status == 'approved') {
                        return '<label class="label label-success label-approved">'
                            . ucfirst($row->status) . '</label>';
                    }
                    elseif ($row->status == 'rejected') {
                        return '<label class="label label-danger label-rejected">'
                            . ucfirst($row->status) . '</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['status', 'action'])
                ->toJson();
        }

        return view('admin.employee_leaves.employee_leave');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_employee_leave'), 403);

        $employees = User::AllEmployees()->get();
        return view('admin.employee_leaves.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreLeave $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeave $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_employee_leave'), 403);

        $leave = new Leave();

        if ($this->user->is_admin) {
            $leave->employee_id = $request->employee;
        }
        else{
            $leave->employee_id = Auth::user()->id;
        }

        $leave->company_id = $this->user->company->id;
        $leave->start_date = $request->startDate;

        if ($request->endDate) {
            $leave->end_date = $request->endDate;
        }

        if ($request->leave_startTime) {
            $start_time = Carbon::createFromFormat('H:i a', $request->leave_startTime, $this->settings->timezone)->setTimezone('UTC');
            $leave->start_time = $start_time->format('H:i:s');
        }
        else {
            $leave->start_time = null;
        }

        if ($request->leave_endTime) {
            $end_time = Carbon::createFromFormat('H:i a', $request->leave_endTime, $this->settings->timezone)->setTimezone('UTC');
            $leave->end_time = $end_time->format('H:i:s');
        }
        else {
            $leave->end_time = null;
        }

        if ($request->half_day == true) {
            $leave->leave_type = 'Half day';
        }
        else {
            $leave->leave_type = 'Full day';
        }

        if ($this->user->is_admin) {
            $leave->status = 'approved';
        }
        else {
            $leave->status = 'pending';
        }

        $leave->reason = $request->reason;
        $leave->save();

        if ($this->user->is_employee) {
            $admins = User::allAdministrators()->get();
            Notification::send($admins, new newLeave($leave));
        }

        return Reply::success(__('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee_leave'), 403);

        $leave = Leave::where('id', $id)->whereHas('employee')->first();
        return view('admin.employee_leaves.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_leave'), 403);

        $leave = Leave::findOrFail($id);
        return view('admin.employee_leaves.edit', compact('leave'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreLeave $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreLeave $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_leave'), 403);

        $leave = Leave::findOrFail($id);
        $leave->start_date = $request->startDate;

        if ($request->endDate) {
            $leave->end_date = $request->endDate;
        }

        if ($request->leave_startTime) {
            $start_time = Carbon::createFromFormat('H:i a', $request->leave_startTime, $this->settings->timezone)->setTimezone('UTC');
            $leave->start_time = $start_time->format('H:i:s');
        }
        else {
            $leave->start_time = null;
        }

        if ($request->leave_endTime) {
            $end_time = Carbon::createFromFormat('H:i a', $request->leave_endTime, $this->settings->timezone)->setTimezone('UTC');
            $leave->end_time = $end_time->format('H:i:s');
        }
        else {
            $leave->end_time = null;
        }

        if ($request->half_day == true) {
            $leave->leave_type = 'Half day';
        }
        else {
            $leave->leave_type = 'Full day';
        }

        $leave->reason = $request->reason;
        $leave->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function updateStatus(Request $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_leave'), 403);

        $leave = Leave::findOrFail($id);

        if (!is_null($request->status)) {
            $leave->status = $request->status;
            $leave->approved_by = Auth::user()->name;
        }

        $leave->save();

        $employee = $leave->employee;
        Notification::send($employee, new updateLeaveStatus($leave));

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_employee_leave'), 403);

        $leave = Leave::findOrFail($id);
        $leave->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

}
