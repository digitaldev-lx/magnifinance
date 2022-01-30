<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\PaymentGatewayCredentials;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\OfflinePayment\StoreOfflinePayment;
use App\Http\Requests\OfflinePayment\UpdateOfflinePayment;
use App\OfflinePaymentMethod;

class PaymentSettingController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.settings'));

    }

    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $credentialSetting = PaymentGatewayCredentials::first();

        return view('superadmin.payment-settings.index', compact('credentialSetting'));
    }

    public function create()
    {
        return view('superadmin.payment-settings.create');
    }

    public function store(StoreOfflinePayment $request)
    {
        $method = OfflinePaymentMethod::create($request->all());

        return Reply::successWithData(__('messages.createdSuccessfully'), ['method' => $method]);
    }

    public function edit($id)
    {
        $method = OfflinePaymentMethod::whereId($id)->firstOrFail();

        return view('superadmin.payment-settings.edit_modal', compact('method'));


    }

    public function update(UpdateOfflinePayment $request, $id)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ];

        OfflinePaymentMethod::whereId($id)->update($data);

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function destroy($id)
    {
        OfflinePaymentMethod::whereId($id)->delete();

        return Reply::success(__('messages.recordDeleted'));
    }

    public function offlinePayments()
    {
        if(request()->ajax()){
            $methods = OfflinePaymentMethod::all();

            return datatables()->of($methods)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    $action .= '<a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-primary btn-circle edit-payment-method"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->editColumn('description', function ($row) {
                    return $row->description;
                })
                ->editColumn('status', function ($row) {

                    $status = $row->status === 'yes' ? 'badge-success' : 'badge-warning';

                    return '<span class="badge '.$status.'">'.strtoupper($row->status).'</span>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->toJson();
        }

        return view('superadmin.payment-settings.offline_payments');
    }

}
