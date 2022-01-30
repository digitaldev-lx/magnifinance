<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Country;
use App\Location;
use App\Helper\Reply;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\Location\StoreLocation;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Location\ChangeLocationRequest;

class LocationController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.locations'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_location','create_location', 'update_location', 'delete_location']));

        if(request()->ajax()){
            $locations = Location::all();

            return datatables()->of($locations)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_location')) {
                        $action .= '<a href="' . route('superadmin.locations.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_location')) {
                        if ($row->count() != 1) {
                            $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                        }
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })->editColumn('country', function ($row) {
                        return $row->country ? $row->country->name : '-';
                })
                ->editColumn('status', function ($row) {
                    $active = $row->status == 'active' ? 'selected' : '';
                    $inactive = $row->status != 'active' ? 'selected' : '';
                    $status = 'disabled';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_location')) {
                        $status = '';
                    }

                    $locationOption = '<select name="location_status" '.$status.' class="form-control location_status" data-location-id="'.$row->id.'">';
                    $locationOption .= '<option '.$active.' value="active">Active</option>';
                    $locationOption .= '<option '.$inactive.' value="inactive">In-Active</option>';
                    $locationOption .= '</select>';

                    return $locationOption;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->toJson();
        }

        return view('superadmin.location.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_location'));

        $countries = Country::all();
        return view('superadmin.location.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreLocation $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLocation $request)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_location'));

        $location = new Location();
        $location->create($request->all());

        return Reply::redirect($request->redirect_url, __('messages.createdSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_location'));

        $this->location = $location;
        $this->countries = Country::all();
        return view('superadmin.location.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreLocation $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreLocation $request, $id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_location'));

        $location = Location::find($id);
        $location->update($request->all());

        return Reply::redirect(route('superadmin.locations.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_location'));

        $location = Location::findOrFail($id);

        $activeLocationCount = Location::active()->count();

        if ($activeLocationCount == 1 && $location->status == 'active') {
            return Reply::error(__('messages.locationActiveOne'));
        }

        $location->destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function changeStatus(ChangeLocationRequest $request)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_location'));

        $location = Location::findOrFail($request->location_id);

        $activeLocationCount = Location::active()->count();

        if ($activeLocationCount == 1 && $location->status == 'active') {
            return Reply::error(__('messages.locationActiveOne'));
        }

        $location->status = $request->location_status;
        $location->save();

        Artisan::call('cache:clear');

        return Reply::success(__('messages.locationStatusChangedSuccessfully'));
    }

}
