<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Coupon;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Coupon\StoreRequest;
use App\Http\Requests\Coupon\UpdateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.coupons'));
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
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_coupon','create_coupon', 'update_coupon', 'delete_coupon']));

        if (\request()->ajax()) {
            $coupon = Coupon::all();

            return \datatables()->of($coupon)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_coupon')) {
                        $action .= '<a href="' . route('superadmin.coupons.edit', [$row->id]) . '" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_coupon')) {
                        $action .= '<a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-coupon" data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_coupon')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    $action .= '</div>';
                    return $action;
                })

                ->editColumn('code', function ($row) {
                    return '<span class="badge badge-warning">' . strtoupper($row->code) . '</span>';
                })
                ->editColumn('title', function ($row) {
                    return strtoupper($row->title);
                })
                ->editColumn('start_date_time', function ($row) {
                    return Carbon::parse($row->start_date_time)->translatedFormat($this->settings->date_format . ' ' . $this->settings->time_format);
                })
                ->editColumn('end_date_time', function ($row) {
                    if ($row->end_date_time) {
                        return Carbon::parse($row->end_date_time)->translatedFormat($this->settings->date_format . ' ' . $this->settings->time_format);
                    }

                    return '-';
                })
                ->editColumn('amount', function ($row) {

                    if ($row->amount && is_null($row->percent)) {
                        return $row->amount;
                    }
                    elseif (is_null($row->amount) && !is_null($row->percent)) {
                        return $row->percent . '%';
                    }
                    elseif (!is_null($row->amount) && !is_null($row->percent)) {
                        return __('app.maxAmountOrPercent', ['percent' => $row->percent, 'maxAmount' => $row->amount]);
                    }

                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="badge badge-success">' . __('app.active') . '</label>';
                    }
                    elseif ($row->status == 'inactive') {
                        return '<label class="badge badge-danger">' . __('app.inactive') . '</label>';
                    }
                    elseif ($row->status == 'expire') {
                        return '<label class="badge badge-danger">' . __('app.expire') . '</label>';
                    }
                })

                ->addIndexColumn()
                ->rawColumns(['action', 'status', 'code'])
                ->make(true);
        }

        return view('superadmin.coupons.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_coupon'));

        $this->days = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];

        return view('superadmin.coupons.create', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_coupon'));

        if (!$request->has('days')) {
            return Reply::error(__('messages.coupon.selectDay'));
        }

        $coupon = new Coupon();
        $data = $request->all();
        $data['start_date_time'] = Carbon::createFromFormat('Y-m-d H:i a', $request->startDate . ' ' . $request->startTime)->format('Y-m-d H:i:s');

        if ($request->end_time) {
            $data['end_date_time'] = Carbon::createFromFormat('Y-m-d H:i a', $request->endDate . ' ' . $request->endTime)->format('Y-m-d H:i:s');
        }

        $data['description'] = clean($request->description);

        $coupon->create($data);

        return Reply::redirect(route('superadmin.coupons.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_coupon'));

        $this->coupon = Coupon::findOrFail($id);

        if ($this->coupon->days) {
            $this->days = json_decode($this->coupon->days);
        }

        return view('superadmin.coupons.show', $this->data);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_coupon'));

        $this->days = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];

        $this->coupon       = Coupon::with('customers')->findOrFail($id);
        $this->selectedDays = json_decode($this->coupon->days);

        return view('superadmin.coupons.edit', $this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_coupon'));

        if (!$request->has('days')) {
            return Reply::error(__('messages.coupon.selectDay'));
        }

        $coupon = Coupon::findOrFail($id);
        $data = $request->all();
        $data['start_date_time'] = Carbon::createFromFormat('Y-m-d H:i a', $request->startDate . ' ' . $request->startTime)->format('Y-m-d H:i:s');

        if ($request->end_time) {
            $data['end_date_time'] = Carbon::createFromFormat('Y-m-d H:i a', $request->endDate . ' ' . $request->endTime)->format('Y-m-d H:i:s');
        }

        $data['description'] = clean($request->description);

        $coupon->update($data);

        return Reply::redirect(route('superadmin.coupons.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_coupon'));

        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return Reply::success(__('messages.recordDeleted'));
    }

}
