<?php

namespace App\Http\Controllers\Admin;

use App\EmployeeSchedule;
use App\User;
use Illuminate\Http\Request;
use App\Helper\Reply;
use Carbon\Carbon;
use App\Http\Controllers\AdminBaseController;

class EmployeeScheduleSettingController extends AdminBaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee_schedule'), 403);

        if (request()->ajax()) {

            $employees = User::AllEmployees()->get();

            return datatables()->of($employees)
                ->addColumn('action', function ($row) {
                        $action = '<div class="text-right"><a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-employee-detail" ><i class="fa fa-eye" aria-hidden="true"></i></a></div>';
                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.employee-schedule.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee_schedule'), 403);

        $schedule = EmployeeSchedule::with('employee')->where('employee_id', $id)->get();

        return view('admin.employee-schedule.show', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_schedule')){
            return Reply::error( __('messages.accessDenied'));
        }

        $updateSchedule = EmployeeSchedule::findOrFail($id);
        $startTime = Carbon::createFromFormat('H:i a', $request->updateStartTime, $this->settings->timezone)->setTimezone('UTC');
        $updateSchedule->start_time = $startTime->format('H:i:s');

        $endTime = Carbon::createFromFormat('H:i a', $request->updateEndTime, $this->settings->timezone)->setTimezone('UTC');
        $updateSchedule->end_time = $endTime->format('H:i:s');


        $updateSchedule->save();

        $schedule = EmployeeSchedule::with('employee')->where('employee_id', $request->empid)->get();

        $tableView = view('admin.employee-schedule.tableview', compact('schedule'))->render();

        return Reply::dataOnly(['html' => $tableView]);

    }

    public function updateWorking(Request $request, $id)
    {
        if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_schedule')){
            return Reply::error( __('messages.accessDenied'));
        }

        $updateworking = EmployeeSchedule::findOrFail($id);

        $updateworking->is_working = $request->isWorking;
        $updateworking->save();

        $schedule = EmployeeSchedule::with('employee')->where('employee_id', $request->empid)->get();

        $tableView = view('admin.employee-schedule.tableview', compact('schedule'))->render();

        return Reply::dataOnly(['html' => $tableView]);

    }

}

