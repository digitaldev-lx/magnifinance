<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\EmployeeGroup;
use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\EmployeeGroup\StoreRequest;
use App\Http\Requests\EmployeeGroup\UpdateRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeGroupController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('app.employeeGroup'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee_group'), 403);

        if(\request()->ajax()){
            $employeeGroup = EmployeeGroup::all();

            return \datatables()->of($employeeGroup)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_group')) {
                        $action .= '<a href="' . route('admin.employee-group.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                          data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_employee_group')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                          data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->editColumn('services', function ($row) {
                    $service_list = '';

                    foreach ($row->services as $key => $service) {
                        $service_list .= '<span class="badge badge-primary username-badge">'.$service->service->name.'</span>';
                    }

                    return $service_list == '' ? '--' : $service_list;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'services'])
                ->toJson();
        }

        return view('admin.employee-group.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_employee_group'), 403);

        $business_services = BusinessService::all();

        return view('admin.employee-group.create', compact('business_services'));
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_employee_group'), 403);

        $company = User::with('company')->where('id', auth()->user()->id)->first();

        $user = new EmployeeGroup();
        $user->company_id = $company->company->id;
        $user->name = $request->name;
        $user->status = 'active';
        $user->save();

        /* Assign services to users */
        $business_service_id = $request->business_service_id;

        if($business_service_id)
        {
            $assignedServices   = array();

            foreach ($business_service_id as $key => $service_id)
            {
                $assignedServices[$key]['business_service_id'] = $business_service_id[$key];
                $assignedServices[$key]['employee_groups_id'] = $user->id;
            }

            DB::table('employee_group_services')->insert($assignedServices);
        }

        return Reply::redirect(route('admin.employee-group.index'), __('messages.createdSuccessfully'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_group'), 403);

        $employeeGroup = EmployeeGroup::where('id', $id)->first();

        /* push all previous assigned services to an array */
        $selectedServices = array();
        $assignedServices = EmployeeGroup::with(['services'])->find($id);

        foreach ($assignedServices->services as $key => $services)
        {
            array_push($selectedServices, $services->service->id);
        }

        $business_services = BusinessService::active()->get();

        return view('admin.employee-group.edit', compact('employeeGroup', 'selectedServices', 'business_services'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee_group'), 403);

        $user = EmployeeGroup::findOrFail($id);

        $user->name = $request->name;
        $user->save();

        /* Assign services to group */
        DB::table('employee_group_services')->where('employee_groups_id', $id)->delete();
        $business_service_id = $request->business_service_id;

        if($business_service_id)
        {
            $assignedServices   = array();

            foreach ($business_service_id as $key => $service_id)
            {
                $assignedServices[$key]['business_service_id'] = $business_service_id[$key];
                $assignedServices[$key]['employee_groups_id'] = $user->id;
            }

            DB::table('employee_group_services')->insert($assignedServices);
        }

        return Reply::redirect(route('admin.employee-group.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_employee_group'), 403);

        EmployeeGroup::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

}
