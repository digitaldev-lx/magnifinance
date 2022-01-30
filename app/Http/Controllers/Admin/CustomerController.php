<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Customer\StoreCustomer;
use App\Notifications\NewUser;
use App\User;
use App\Http\Requests\Customer\UpdateCustomer;
use App\Role;

class CustomerController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.customers'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer'), 403);

        $recordsLoad = 8;
        $params = \request('param');

        if (\request()->ajax()) {
            $totalRecords = User::withoutGlobalScopes()->with('customerBookings')->has('customerBookings')->orderBy('id', 'desc')->search($params)->count();

            $customers = User::withoutGlobalScopes()->with('customerBookings')->has('customerBookings')->search($params)->take(\request('take'))->orderBy('id', 'desc')->get();

            $view = view('admin.customer.list_ajax', compact('customers', 'totalRecords', 'recordsLoad'))->render();
            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }

        return view('admin.customer.index', compact('recordsLoad'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCustomer $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomer $request)
    {
        $data = [
            'company_id' => null,
            'name' => $request->name,
            'email' => $request->email,
            'calling_code' => $request->calling_code,
            'mobile' => $request->mobile,
            'password' => '123456',
        ];

        $user = User::create($data);

        // add customer role
        $user->attachRole(Role::where('name', 'customer')->withoutGlobalScopes()->first()->id);

        $user->notify(new NewUser('123456'));

        return Reply::successWithData(__('messages.createdSuccessfully'), ['user' => ['id' => $user->id, 'text' => $user->name]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $customer = User::withoutGlobalScopes()->findOrFail($id);

        if (\request()->ajax()) {
            $view = view('admin.customer.ajax_show', compact('customer'))->render();
            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }

        $completedBookings = Booking::withoutGlobalScopes()->where('user_id', $id)->where('status', 'completed')->count();
        $approvedBookings = Booking::withoutGlobalScopes()->where('user_id', $id)->where('status', 'approved')->count();
        $pendingBookings = Booking::withoutGlobalScopes()->where('user_id', $id)->where('status', 'pending')->count();
        $canceledBookings = Booking::withoutGlobalScopes()->where('user_id', $id)->where('status', 'canceled')->count();
        $inProgressBookings = Booking::withoutGlobalScopes()->where('user_id', $id)->where('status', 'in progress')->count();
        $earning = Booking::withoutGlobalScopes()->where('user_id', $id)->where('status', 'completed')->sum('amount_to_pay');

        return view('admin.customer.show', compact('customer', 'completedBookings', 'approvedBookings', 'pendingBookings', 'inProgressBookings', 'canceledBookings', 'earning'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_customer'), 403);

        $customer = User::withoutGlobalScopes()->find($id);
        return view('admin.customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCustomer $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomer $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_customer'), 403);

        $user = User::withoutGlobalScopes()->find($id);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password != '') {
            $user->password = $request->password;
        }

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image, 'avatar');
        }

        $user->save();

        return Reply::redirect(route('admin.customers.show', $id), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_customer'), 403);

        User::withoutGlobalScopes()->findOrFail($id)->delete();
        return Reply::redirect(route('admin.customers.index'), __('messages.recordDeleted'));
    }

}
