<?php

namespace App\Http\Controllers\Admin;

use App\Tax;
use App\Deal;
use App\ItemTax;
use App\DealItem;
use App\Location;
use Carbon\Carbon;
use App\Helper\Files;
use App\Helper\Reply;
use App\BusinessService;
use Illuminate\Support\Arr;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Deal\CreateDeal;
use App\Http\Requests\Deal\StoreRequest;
use App\Http\Requests\Deal\UpdateRequest;
use App\Http\Controllers\AdminBaseController;

class DealController extends AdminBaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.deals'));
    }

    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_deal'), 403);

        if(request()->ajax())
        {
            $deals = Deal::with('location')->orderBy('id', 'desc')->get();

            return datatables()->of($deals)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if($this->user->can('update_deal')) {
                        $action .= '<a href="' . route('admin.deals.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if($this->user->can('create_deal')) {
                        $action .= ' <a href="javascript:;" class="btn btn-warning btn-circle duplicate-row"
                    data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.duplicate').'"><i class="fa fa-clone" aria-hidden="true"></i></a>';
                    }

                    $action .= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-deal"
                    data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-eye" aria-hidden="true"></i></a> ';



                    if($this->user->can('delete_deal')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="'.$row->deal_image_url.'" class="img" width="120em"/> ';
                })
                ->editColumn('title', function ($row) {
                    return ucfirst($row->title);
                })
                ->editColumn('start_date_time', function ($row) {
                    return Carbon::parse($row->start_date_time)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format);

                })
                ->editColumn('end_date_time', function ($row) {
                    return Carbon::parse($row->end_date_time)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format);
                })
                ->editColumn('original_amount', function ($row) {
                    return currencyFormatter($row->original_amount, myCurrencySymbol());
                })
                ->editColumn('deal_amount', function ($row) {
                    return currencyFormatter($row->deal_amount, myCurrencySymbol());
                })
                ->editColumn('status', function ($row) {

                    if(Carbon::now()->timezone($this->settings->timezone)->gt( $row->end_date_time)){
                        return '<label class="badge badge-danger">'.__('app.expired').'</label>';
                    }
                    elseif($row->status == 'active'){
                        return '<label class="badge badge-success">'.__('app.active').'</label>';
                    }
                    elseif($row->status == 'inactive'){
                        return '<label class="badge badge-danger">'.__('app.inactive').'</label>';
                    }

                })
                ->editColumn('usage', function ($row) {
                    $used_time = $row->used_time;
                    $uses_limit = $row->uses_limit;

                    if($used_time == ''){
                        $used_time = 0;
                    }

                    if($uses_limit == 0){
                        $uses_limit = '&infin;';
                    }

                    return $used_time.'/'.$uses_limit;
                })
                ->addColumn('deal_location', function ($row) {
                    return $row->location->name;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'status', 'usage'])
                ->toJson();
        }

        return view('admin.deals.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateDeal $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_deal'), 403);

        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $locations = Location::withoutGlobalScope(CompanyScope::class)->groupBy('name')->get();
        $services = BusinessService::with('location')->orderBy('name')->get();
        $taxes = Tax::active()->get();

        $variables = compact('days', 'taxes', 'locations', 'services');

        if ($request->deal_id) {
            $deal = Deal::with('location', 'services')->findOrFail($request->deal_id);
            $variables = Arr::add($variables, 'deal', $deal);
            $selectedDays = json_decode($deal->days);
            $variables = Arr::add($variables, 'selectedDays', $selectedDays);

            $deal_services = $deal->services()->pluck('business_service_id')->toArray();
            $variables = Arr::add($variables, 'deal_services', $deal_services);

            $deal_items = $deal->services()->with('businessService')->get();
            $variables = Arr::add($variables, 'deal_items', $deal_items);

            $deal_items_table = view('admin.deals.deal_items_edit', compact('deal_items'))->render();
            $variables = Arr::add($variables, 'deal_items_table', $deal_items_table);
        }

        return view('admin.deals.create', $variables);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_deal'), 403);

        if($this->total_deals >= $this->package->max_deals)
        {
            return Reply::error( __('messages.maxDealLimit.'));
        }

        if(!$request->has('days')){
            return Reply::error( __('messages.coupon.selectDay'));
        }

        $services = $request->services;
        $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->deal_startDate)->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('Y-m-d H:i a', $request->deal_endDate)->format('Y-m-d H:i:s');
        $startTime = Carbon::createFromFormat('H:i a', $request->deal_startTime)->format('H:i:s');
        $endTime  = Carbon::createFromFormat('H:i a', $request->deal_endTime)->format('H:i:s');


        $deal = new Deal();
        $deal->title                   = $request->title;
        $deal->slug                    = $request->slug;
        $deal->start_date_time         = $startDate;
        $deal->end_date_time           = $endDate;
        $deal->open_time               = $startTime;
        $deal->close_time              = $endTime;
        $deal->max_order_per_customer  = $request->customer_uses_time;
        $deal->status                  = $request->status;
        $deal->days                    = json_encode($request->days);
        $deal->description             = clean($request->description);
        $deal->location_id             = $request->locations;
        $deal->deal_applied_on         = $request->choice;
        $deal->discount_type           = $request->discount_type;
        $deal->percentage              = $request->discount;

        if($request->uses_time == ''){
            $deal->uses_limit          = 0;
        }

        $deal->uses_limit = $request->uses_time;

        if(count($services) > 1){
            $deal->deal_type           = 'Combo';
        }

        if ($request->hasFile('feature_image')) {
            $deal->image = Files::upload($request->feature_image, 'deal');
        }

        /* Save deal */
        $deal_services = $request->deal_services;
        $prices = $request->deal_unit_price;
        $quantity = $request->deal_quantity;
        $discount = $request->deal_discount;

        $discountAmount = 0;
        $amountToPay    = 0;
        $originalAmount = 0;
        $dealItems      = array();

        foreach ($deal_services as $key => $service){
            $amount = ($quantity[$key] * $prices[$key]);
            $dealItems[] = [
                'business_service_id'   => $deal_services[$key],
                'quantity'              => $quantity[$key],
                'unit_price'         => $prices[$key],
                'discount_amount'       => $discount[$key],
                'total_amount'          => $amount - $discount[$key],
            ];
            $originalAmount = ($originalAmount + $amount);
            $discountAmount = ($discountAmount + $discount[$key]);
        }

        $amountToPay = $originalAmount - $discountAmount;

        $deal->deal_amount             = $amountToPay;
        $deal->original_amount         = $originalAmount;

        $deal->save();

        /* Save deal items */
        foreach ($dealItems as $key => $dealItem){
            $dealItems[$key]['deal_id'] = $deal->id;
        }

        DB::table('deal_items')->insert($dealItems);

        /* store taxes */
        $tax_ids = $request->tax_ids;

        if($tax_ids !== null)
        {
            foreach ($tax_ids as $key => $tax_id)
            {
                $taxService = new ItemTax();
                $taxService->company_id = company()->id;
                $taxService->tax_id = $tax_id;
                $taxService->deal_id = $deal->id;
                $taxService->save();
            }
        }

        return Reply::redirect(route('admin.deals.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $deal = Deal::with('location')->findOrFail($id);
        $deal_items = DealItem::with('businessService')->where('deal_id', $id)->get();

        $days = [];

        if($deal->days){
            $days = json_decode($deal->days);
        }

        $selectedTax = array();
        $dealsTax = ItemTax::where('deal_id', $id)->get();

        foreach ($dealsTax as $key => $dealsTaxes)
        {
            $tax = Tax::where('id', $dealsTaxes->tax_id)->first();
            array_push($selectedTax, $tax);
        }

        return view('admin.deals.show', compact('deal', 'selectedTax', 'days', 'deal_items'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_deal'), 403);

        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        $deal = Deal::with('location', 'services')->findOrFail($id);
        $selectedDays = json_decode($deal->days);

        $services = BusinessService::orderBy('name')->get();
        $deal_services = $deal->services()->pluck('business_service_id')->toArray();

        $deal_items = $deal->services()->with('businessService')->get();

        $deal_items_table = view('admin.deals.deal_items_edit', compact('deal_items'))->render();

        $selectedTax = array();
        $taxServices = ItemTax::where('deal_id', $deal->id)->get();

        foreach ($taxServices as $key => $taxService)
        {
            array_push($selectedTax, $taxService->tax_id);
        }

        $taxes = Tax::active()->get();

        return view('admin.deals.edit', compact('days', 'taxes', 'selectedTax', 'deal', 'selectedDays', 'services', 'deal_services', 'deal_items_table'));
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

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_deal'), 403);

        if(!$request->has('days')){
            return Reply::error( __('messages.coupon.selectDay'));
        }

        /* delete all items from deal_items table */
        DB::table('deal_items')->where('deal_id', $id)->delete();

        $services = $request->services;
        $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->deal_startDate)->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('Y-m-d H:i a', $request->deal_endDate)->format('Y-m-d H:i:s');
        $startTime = Carbon::createFromFormat('H:i a', $request->deal_startTime)->format('H:i:s');
        $endTime  = Carbon::createFromFormat('H:i a', $request->deal_endTime)->format('H:i:s');

        $deal = Deal::findOrFail($id);
        $deal->title                   = $request->title;
        $deal->slug                    = $request->slug;
        $deal->start_date_time         = $startDate;
        $deal->end_date_time           = $endDate;
        $deal->open_time               = $startTime;
        $deal->close_time              = $endTime;
        $deal->max_order_per_customer  = $request->customer_uses_time;
        $deal->status                  = $request->status;
        $deal->days                    = json_encode($request->days);
        $deal->description             = clean($request->description);
        $deal->location_id             = $request->locations;
        $deal->deal_applied_on         = $request->choice;
        $deal->discount_type           = $request->discount_type;
        $deal->percentage              = $request->discount;

        if($request->uses_time == ''){
            $deal->uses_limit          = 0;
        }

        $deal->uses_limit = $request->uses_time;

        if(count($services) > 1){
            $deal->deal_type           = 'Combo';
        }

        if ($request->hasFile('feature_image')) {
            $deal->image = Files::upload($request->feature_image, 'deal');
        }

        /* Save deal */
        $deal_services = $request->deal_services;
        $prices = $request->deal_unit_price;
        $quantity = $request->deal_quantity;
        $discount = $request->deal_discount;

        $discountAmount = 0;
        $amountToPay    = 0;
        $originalAmount = 0;
        $dealItems      = array();

        foreach ($deal_services as $key => $service){
            $amount = ($quantity[$key] * $prices[$key]);
            $dealItems[] = [
                'business_service_id'   => $deal_services[$key],
                'quantity'              => $quantity[$key],
                'unit_price'            => $prices[$key],
                'discount_amount'       => $discount[$key],
                'total_amount'          => $amount - $discount[$key],
            ];
            $originalAmount = ($originalAmount + $amount);
            $discountAmount = ($discountAmount + $discount[$key]);
        }

        $amountToPay = $originalAmount - $discountAmount;

        $deal->deal_amount             = $amountToPay;
        $deal->original_amount         = $originalAmount;

        $deal->save();

        /* Save deal items */
        foreach ($dealItems as $key => $dealItem){
            $dealItems[$key]['deal_id'] = $deal->id;
        }

        DB::table('deal_items')->insert($dealItems);

        /* delete existing taxes */
        $taxServices = ItemTax::where('deal_id', $id)->get();

        foreach ($taxServices as $key => $taxService)
        {
            ItemTax::destroy($taxService->id);
        }

        /* update taxes */
        $tax_ids = $request->tax_ids;

        if($tax_ids !== null)
        {
            foreach ($tax_ids as $key => $tax_id)
            {
                $taxService = new ItemTax();
                $taxService->company_id = company()->id;
                $taxService->tax_id = $tax_id;
                $taxService->deal_id = $deal->id;
                $taxService->save();
            }
        }

        return Reply::redirect(route('admin.deals.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_deal'), 403);

        $coupon = Deal::findOrFail($id);
        $coupon->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

    public function selectLocation(Request $request)
    {
        $locations = [];
        $selected_location  = '';

        if ($request->has('services')) {
            $locations = Location::withoutGlobalScope(CompanyScope::class)->whereHas('services', function ($query) use($request){
                return $query->whereIn('id', $request->services);
            })->get();
        }

        $selected_location .= '<option selected value="">'.__('app.selectLocation').'</option>';

        if (count($locations) == 1) {

            foreach ($locations as $location)
            {
                $selected_location .= '<option value="'.$location->id.'">'.$location->name.'</option>';
            }

        }

        return response()->json(['selected_location' => $selected_location]);

    } /* end of selectLocation() */

    public function selectServices(Request $request)
    {
        $selected_service = '';

        $services = BusinessService::with('location')->whereHas('location', function ($query) use ($request){
            $query->whereId($request->locations);
        })->get();

        $selected_service = '<option selected value="">Select Services</option>';

        foreach ($services as $service){
            $selected_service .= "<option value='".$service->id."'>".$service->name.'</option>';
        }

        return response()->json(['selected_service' => $selected_service]);
    } /* end of selectServices() */

    public function resetSelection()
    {
        $all_services_array = '<option value="">Select Services</option>';
        $services = BusinessService::with('location')->get();

        foreach ($services as $service)
        {
            $all_services_array .= '<option value="'.$service->id.'">'.$service->name.' ('.$service->location->name.') '.'</option>';
        }

        $all_locations_array = '<option selected value="">Select Location</option>';
        $locations = Location::all();

        foreach ($locations as $location)
        {
            $all_locations_array .= '<option value="'.$location->id.'">'.$location->name.'</option>';
        }

        return response()->json(['all_locations_array' => $all_locations_array, 'all_services_array' => $all_services_array]);
    } /* end of resetSelection()  */

    public function makeDeal(Request $request)
    {
        $services = $request->services;
        $location = $request->locations;

        $deal_list = BusinessService::whereIn('id', $services)->with('location')
            ->whereHas('location', function($query) use($location){
                $query->whereId($location);
            })->get();

        $view = view('admin.deals.deal_items', compact('deal_list'))->render();

        return response()->json(['view' => $view]);
    }

} /* end of class */
