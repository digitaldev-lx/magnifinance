<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Spotlight\StoreSpotlight;
use App\Company;
use App\Deal;
use App\Spotlight;
use App\Helper\Reply;

class SpotlightController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.spotlight'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_403(!$this->user->is_superadmin);

        if (request()->ajax()) {
            $spotLight = Spotlight::with('company', 'deal')->orderBy('sequence', 'asc')->get();

            return datatables()->of($spotLight)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';
                    $action .= '<a href="' . route('superadmin.spotlight-deal.edit', [$row->id]) . '" class="btn btn-primary btn-circle edit-language"
                          data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-spotlight-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('company_name', function ($row) {
                    return strtolower($row->company->company_name);
                })
                ->editColumn('deal_name', function ($row) {
                    return ucfirst($row->deal->title);
                })
                ->editColumn('from_date', function ($row) {
                    return ucfirst(\Carbon\Carbon::parse($row->from_date)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format));
                })
                ->editColumn('to_date', function ($row) {
                    return ucfirst(\Carbon\Carbon::parse($row->to_date)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format));
                })
                ->editColumn('sequence', function ($row) {
                    $options = '';

                    $total_sequence = $row->select('sequence', 'id')->orderBy('sequence', 'asc')->get();

                    $options = '<select class="form-control spotlight-sequence" id="' . $row->id . '">';

                    foreach ($total_sequence as $total_sequences) {

                        $selected = '';

                        if ($total_sequences->sequence == $row->sequence) {
                            $selected = 'selected';
                        }

                        $options .= '<option value="' . $total_sequences->sequence . '" ' . $selected . '>' . $total_sequences->sequence . '</option>';
                    }

                    $options .= '</select>';

                    return $options;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'sequence'])
                ->toJson();
        }

        return view('superadmin.spotlight.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = Company::all();

        return view('superadmin.spotlight.create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreSpotlight $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSpotlight $request)
    {
        $allvalue = Spotlight::count();

        if ($allvalue == 0) {

            $spotLight = new Spotlight;

            $spotLight->company_id = $request->company;
            $spotLight->deal_id = $request->deal;
            $spotLight->from_date = $request->fromDate;
            $spotLight->to_date = $request->toDate;
            $spotLight->sequence = '1';
            $spotLight->save();
        }
        else {

            $lastValue = Spotlight::orderBy('sequence', 'desc')->first();
            $checkDeal_id = spotlight::where('deal_id', $request->deal)->first();

            if (!is_null($checkDeal_id)) {
                return Reply::error(__('messages.spotlightAlreadyCreated'));
            }
            else {
                $spotLight = new Spotlight;
                $spotLight->company_id = $request->company;
                $spotLight->deal_id = $request->deal;
                $spotLight->from_date = $request->fromDate;
                $spotLight->to_date = $request->toDate;
                $spotLight->sequence = $lastValue->sequence + 1;
                $spotLight->save();
            }
        }

        return Reply::success(__('messages.createdSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $spotlight = Spotlight::where('id', $id)->first();
        $company = Company::all();
        $deal = Deal::all();

        return view('superadmin.spotlight.edit', compact('company', 'spotlight', 'deal'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreSpotlight $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSpotlight $request, $id)
    {
        $spotLight = Spotlight::findorfail($id);
        $checkDeal_id = spotlight::where('deal_id', $request->deal)->first();

        $spotLight->company_id = $request->company;

        if (!is_null($checkDeal_id)) {
            if ($checkDeal_id->id == $id) {

                $spotLight->deal_id = $request->deal;
                $spotLight->from_date = $request->fromDate;
                $spotLight->to_date = $request->toDate;
                $spotLight->save();
                return Reply::success(__('messages.updatedSuccessfully'));
            }

            return Reply::error(__('messages.spotlightAlreadyCreated'));
        }

        $spotLight->deal_id = $request->deal;
        $spotLight->from_date = $request->fromDate;
        $spotLight->to_date = $request->toDate;
        $spotLight->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $spotLight = Spotlight::findOrFail($id);

        /* check for Sequence numbers greater then sequence number of that Id: */
        $decrement_sequence_number = Spotlight::where('sequence', '>', $spotLight->sequence)->get();

        foreach ($decrement_sequence_number  as  $decrement_sequence_numbers) {

            $decrement_sequence_numbers->sequence = ((int)$decrement_sequence_numbers->sequence - 1);
            $decrement_sequence_numbers->save();
        }

        $spotLight->delete();

        return Reply::success(__('messages.recordDeleted'));
    }

    public function getdeal(Request $request, $id)
    {
        $deal = Deal::where('company_id', $id)->pluck('title', 'id');

        return json_encode($deal);
    }

    public function updateSequence(Request $request, $id)
    {
        $currentSequence = Spotlight::where('id', $id)->first();

        if ($currentSequence->sequence > $request->sequence) {
            /* check for Sequence numbers less then current sequence: */
            $increment_sequence_number = Spotlight::where('sequence', '<', $currentSequence->sequence)->where('sequence', '>=', $request->sequence)->get();

            foreach ($increment_sequence_number as  $increment_sequence_numbers) {

                $increment_sequence_numbers->sequence = ((int)$increment_sequence_numbers->sequence + 1);
                $increment_sequence_numbers->save();
            }

            $sequence = Spotlight::findOrFail($id);
            $sequence->sequence = $request->sequence;
            $sequence->save();
        }
        else {
            /* check for Sequence numbers greater then current sequence: */

            $decrement_sequence_number = Spotlight::where('sequence', '>', $currentSequence->sequence)->where('sequence', '<=', $request->sequence)->get();

            foreach ($decrement_sequence_number  as  $decrement_sequence_numbers) {

                $decrement_sequence_numbers->sequence = ((int)$decrement_sequence_numbers->sequence - 1);
                $decrement_sequence_numbers->save();
            }

            $sequence = Spotlight::findOrFail($id);
            $sequence->sequence = $request->sequence;
            $sequence->save();
        }

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
