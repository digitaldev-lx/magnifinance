<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Company;
use App\Currency;
use App\Helper\Files;
use App\Helper\Formats;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Company\StoreCompany;
use App\Http\Requests\Company\UpdateCompany;
use App\Http\Requests\Package\ChangePackage;
use App\Language;
use App\OfflineInvoice;
use App\OfflinePaymentMethod;
use App\Package;
use App\Role;
use App\User;
use Carbon\Carbon;
use DigitalDevLX\LaravelMySqlSpatial\Types\Point;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CompanyController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.companies'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_company','create_company', 'update_company', 'delete_company']));

        if (\request()->ajax()) {
            $companies = Company::all();

            return \datatables()->of($companies)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_company')) {
                        $action .= '<a href="javascript:;" class="btn btn-dark btn-circle" id="login-to-vendor"
                        data-toggle="tooltip" data-company-id="' . $row->id . '" data-original-title="' . __('app.loginToVendor') . '"><i class="fa fa-key" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_company')) {
                        $action .= ' <a href="javascript:;" class="btn btn-info btn-circle view-company"
                        data-toggle="tooltip" data-company-id="' . $row->id . '" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_company')) {
                        $action .= ' <a href="' . route('superadmin.companies.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                        data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_company')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->addColumn('logo', function ($row) {
                    return '<img src="' . $row->logo_url . '"  width="120em" /> ';
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->company_name);
                })
                ->editColumn('email', function ($row) {
                    return $row->company_email;
                })
                ->editColumn('package', function ($row) {
                    $package = '';
                    $package .= $row->package->name;

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_company')) {

                        $package .= ' <br><a href="javascript:;" class="label label-custom package-update-button" data-toggle="modal" data-target="#myModal" data-company-id="' . $row->id . '" data-original-title="Change">
                        <i class="fa fa-edit" aria-hidden="true"></i> ' . __('app.change') . '</a>';
                    }

                    return $package;
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
                ->rawColumns(['package', 'action', 'logo', 'status'])
                ->toJson();
        }

        $totalCompanies = Company::select('id')->first();
        $activeCompanies = Company::where('status', '=', 'active')->count();
        $deActiveCompanies = Company::where('status', '=', 'inactive')->count();

        return view('superadmin.company.index', compact('totalCompanies', 'activeCompanies', 'deActiveCompanies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_company'));

        $this->dateFormats = Formats::dateFormats();
        $this->timeFormats = Formats::timeFormats();
        $this->dateObject = Carbon::now($this->settings->timezone);
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->currencies = Currency::all();
        $this->enabledLanguages = Language::where('status', 'enabled')->orderBy('language_name')->get();

        return view('superadmin.company.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCompany $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompany $request)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_company'));
        DB::beginTransaction();

        $data = $request->all();

        if ($request->hasFile('logo')) {
            $data['logo'] = Files::upload($request->logo, 'company-logo');
        }
        $data['lat_long'] = new Point($request->latitude, $request->longitude);	// (lat, lng),
        $company = Company::create($data);

        $user = $company->user()->create($request->all());

        $adminRole = Role::select('id', 'name')
            ->where(['name' => 'administrator', 'company_id' => $company->id])
            ->first()->id;

        $user->attachRole($adminRole);

        DB::commit();
        return Reply::redirect(route('superadmin.companies.index'), __('messages.companyCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->methods = OfflinePaymentMethod::all();
        $this->currentPackage = Company::with('package')->find($id);
        $this->allPackages = Package::select('id', 'name', 'monthly_price', 'annual_price')->where('status', '=', 'active')->get();

        return view('superadmin.company.package-modal', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_company'));

        $this->dateFormats = Formats::dateFormats();
        $this->timeFormats = Formats::timeFormats();
        $this->dateObject = Carbon::now($this->settings->timezone);
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->currencies = Currency::all();
        $this->enabledLanguages = Language::where('status', 'enabled')->orderBy('language_name')->get();
        $this->company = $company;

        return view('superadmin.company.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCompany  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompany $request, Company $company)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_company'));
        $data = $request->all();

        if ($request->hasFile('logo')) {
            $data['logo'] = Files::upload($request->logo, 'company-logo');
        }
        $data['lat_long'] = new Point($request->latitude, $request->longitude);
        $company->update($data);

        $company->status = $request->status;
        $company->save();

        return Reply::redirect(route('superadmin.companies.index'), __('messages.companyUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Company::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function changePackage(ChangePackage $request)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_company'));

        $company = Company::findOrFail($request->companyId);

        $company->package_id = $request->packageId;
        $company->save();

        // generate invoice for the package assigned
        $invoice = new OfflineInvoice;

        $invoice['company_id'] = $request->companyId;
        $invoice['package_id'] = $request->packageId;
        $invoice['offline_method_id'] = $request->paymentMethod;
        $invoice['amount'] = $request->amount;
        $invoice['pay_date'] = $request->payDate;
        $invoice['next_pay_date'] = $request->licenceExpireDate;
        $invoice['status'] = 'paid';
        $invoice['package_type'] = $request->packageType;

        $invoice->save();

        return Reply::success(__('messages.packages.packageChangedSuccessfully'));
    }

    public function showCompanyDetails($id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_company'));

        $this->employees = User::otherThanCustomers()->count();
        $this->company = Company::with('package', 'currency')->where('id', $id)->first();
        return view('superadmin.company.show', $this->data);
    }

    public function loginAsVendor($companyId)
    {
        $user = $this->user;
        $admin = User::AllAdministrators()->where('company_id', $companyId)->first();

        Session::flush();
        Auth::logout();
        Auth::loginUsingId($admin->id);

        return Reply::success(__('messages.successfullyLoginAsVendor'));
    }

}
