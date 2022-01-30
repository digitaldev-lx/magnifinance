<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Tax\StoreTax;
use App\Tax;

class TaxSettingController extends SuperAdminBaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {

            $tax = Tax::all();

            return \datatables()->of($tax)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';
                    $action .= '<a href="javascript:;" class="btn btn-primary btn-circle edit-tax" data-row-id="' . $row->id . '"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-tax"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                        $action .= '</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->editColumn('percent', function ($row) {
                    return $row->percent;
                })
                ->editColumn('status', function ($row) {
                    if($row->status == 'active'){
                        return '<label class="badge badge-success">'.__('app.active').'</label>';
                    }
                    elseif($row->status == 'inactive'){
                        return '<label class="badge badge-danger">'.__('app.deactive').'</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->toJson();
        }

        return view('superadmin.tax-settings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.tax-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTax $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTax $request)
    {
        $tax = new Tax();
        $tax->name = $request->tax_name;
        $tax->percent = $request->percent;
        $tax->status = $request->status;
        $tax->save();

        return Reply::success( __('messages.createdSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->tax = Tax::find($id);
        return view('superadmin.tax-settings.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreTax $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTax $request, $id)
    {
        $tax = Tax::find($id);
        $tax->name = $request->tax_name;
        $tax->percent = $request->percent;
        $tax->status = $request->status;
        $tax->save();

        return Reply::success( __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Tax::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

}
