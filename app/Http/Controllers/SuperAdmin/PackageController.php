<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Package\StorePackage;
use App\Package;
use App\PackageModules;
use App\PaymentGatewayCredentials;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PackageController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.packages'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_package','create_package', 'update_package', 'delete_package']));

        if (\request()->ajax()) {
            $package = Package::where(function ($q) {
                $q->where('type', null)->orWhere('type', 'default');
            })->get();

            return \datatables()->of($package)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_package')) {

                        $action .= '<a href="' . route('superadmin.packages.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                        data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_package')) {
                        if ($row->type != 'default') {
                            $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                        }
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    $badgeIcon = $row->name;

                    if ($row->make_private == 'true') {
                        $badgeIcon .= ' <i data-toggle="tooltip" data-original-title="Private" class="fa fa-lock private-package"></i>';
                    }

                    if ($row->mark_recommended == 'true') {
                        $badgeIcon .= ' <span class="badge badge-pill badge-info recommended-package">' . __('Recommended') . '</span>';
                    }

                    return $badgeIcon;
                })
                ->editColumn('max_employees', function ($row) {
                    return $row->max_employees;
                })
                ->editColumn('max_services', function ($row) {
                    return $row->max_services;
                })
                ->editColumn('max_deals', function ($row) {
                    return $row->max_deals;
                })
                ->editColumn('max_roles', function ($row) {
                    return $row->max_roles;
                })
                ->editColumn('package_modules', function ($row) {

                    if (is_null($row->package_modules) || $row->package_modules == 'null') {
                        return '--';
                    }

                    $arr = json_decode($row->package_modules, true);
                    $data = '<ul>';

                    foreach ($arr as $key => $value) {
                        $data .= '<li>' . $value . '</li>';
                    }

                    return $data .= '</ul>';
                })
                ->editColumn('status', function ($row) {

                    if ($row->status == 'active') {
                        return '<label class="badge badge-success">' . __('app.active') . '</label>';
                    }
                    elseif ($row->status == 'inactive') {
                        return '<label class="badge badge-danger">' . __('app.inactive') . '</label>';
                    }
                })

                ->addIndexColumn()
                ->rawColumns(['action', 'name', 'status', 'max_employees', 'package_modules'])
                ->make(true);
        }

        $totalPackages = Package::select('id')->where('name', '!=', 'Trial')->count();

        $activePackages = Package::where('status', '=', 'active')->where('name', '!=', 'Trial')->count();

        $deActivePackages = Package::where('status', '=', 'inactive')->where('name', '!=', 'Trial')->count();

        return view('superadmin.packages.index', compact('totalPackages', 'activePackages', 'deActivePackages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_package'));

        $this->package_modules = PackageModules::get();
        $this->paymentCredentials = PaymentGatewayCredentials::first();

        return view('superadmin.packages.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePackage $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePackage $request)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_package'));

        $package = new Package();
        $data = $this->modifyRequest($request);
        $package->create($data);

        return Reply::redirect(route('superadmin.packages.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function edit(Package $package)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_package'));

        $arr = json_decode($package->package_modules, true);
        $data = [];

        if (!is_null($arr)) {
            foreach ($arr as $key => $value) {
                $data[] = $value;
            }
        }

        $this->selected_package_modules = $data;
        $this->package = $package;
        $this->package_modules = PackageModules::get();

        return view('superadmin.packages.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StorePackage $request
     * @param  \App\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(StorePackage $request, Package $package)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_package'));

        $data = $this->modifyRequest($request);
        $package->update($data);

        return Reply::redirect(route('superadmin.packages.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_package'));

        $package = Package::findOrFail($id);
        $package->delete();

        return Reply::success(__('messages.recordDeleted'));
    }

    private function modifyRequest($request)
    {
        $data = $request->all();
        $data['make_private'] = is_null($request->make_private) ? 'false' : $request->make_private;
        $data['mark_recommended']  = is_null($request->mark_as_recommended) ? 'false' : $request->mark_as_recommended;
        $data['status']  = is_null($request->status) ? 'inactive' : $request->status;
        $data['package_modules']  = json_encode($request->package_modules);
        return $data;
    }

}
