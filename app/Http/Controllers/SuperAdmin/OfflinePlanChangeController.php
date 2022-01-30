<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Company;
use App\GlobalSetting;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Notifications\OfflinePackageChangeConfirmation;
use App\Notifications\OfflinePackageChangeReject;
use App\OfflineInvoice;
use App\OfflinePlanChange;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class OfflinePlanChangeController extends SuperAdminBaseController
{

    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.offlinePlanChange'));
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_company','create_company', 'update_company', 'delete_company']));

        $this->pageTitle = 'Offline Plan Change';
        $this->global = GlobalSetting::first();
        $this->totalRequest = OfflinePlanChange::count();

        return view('superadmin.offline-plan-change.index', $this->data);
    }

    public function data(Request $request)
    {
        $users = OfflinePlanChange::with('company', 'package', 'offlineMethod');
        return DataTables::of($users)
            ->addColumn('action', function($row) {
                $string = '<div class="text-right">';

                if($row->status == 'pending') {
                    $string .= '<a href="javascript:;" data-id="'.$row->id.'" class="btn btn-info btn-circle accept-offline-plan-change"
                      data-toggle="tooltip" data-original-title="Verify"><i class="fa fa-check" aria-hidden="true"></i></a>
                      <a href="javascript:;" data-id="'.$row->id.'" class="btn btn-danger btn-circle reject-offline-plan-change"
                      data-toggle="tooltip" data-original-title="Reject"><i class="fa fa-remove" aria-hidden="true"></i></a>';

                }

                $string .= '<a href="'.$row->file.'" target="_blank" class="btn btn-default btn-circle m-l-5"
                      data-toggle="tooltip" data-original-title="View File"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                $string .= '</div>';

                return $string;
            })
            ->editColumn(
                'status',
                function ($row) {
                    $status = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
                    return '<label class="badge badge-'.$status[$row->status].'">'.ucwords($row->status).'</label>';

                }
            )
            ->rawColumns(['name', 'action', 'status', 'file_name'])
            ->make(true);
    }

    public function verify(Request $request)
    {
        $offlinePlanChnage = OfflinePlanChange::findOrFail($request->id);
        $invoice = OfflineInvoice::findOrFail($offlinePlanChnage->invoice_id);
        $company = Company::find($offlinePlanChnage->company_id);

        // Change company package
        $company->package_id = $offlinePlanChnage->package_id;
        $company->package_type = $offlinePlanChnage->package_type;
        $company->save();

        // set status of invoice paid
        $invoice->status = 'paid';
        $invoice->save();

        // set status of request verified
        $offlinePlanChnage->status = 'verified';
        $offlinePlanChnage->save();

        $admins = User::allAdministrators()->where('company_id', $offlinePlanChnage->company_id)->get();
        Notification::send($admins, new OfflinePackageChangeConfirmation($offlinePlanChnage));

        return Reply::success('Request successfully verified');
    }

    public function reject(Request $request)
    {
        $offlinePlanChnage = OfflinePlanChange::findOrFail($request->id);
        $company = Company::find($offlinePlanChnage->company_id);
        // set status of request verified
        $offlinePlanChnage->status = 'rejected';
        $offlinePlanChnage->save();

        $admins = User::allAdministrators()->where('company_id', $company->id)->get();
        Notification::send($admins, new OfflinePackageChangeReject($offlinePlanChnage));

        return Reply::success('Request successfully rejected');
    }

}
