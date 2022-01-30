<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Booking;
use App\Comment;
use App\GlobalSetting;
use App\Helper\Reply;
use App\Rating;
use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdminBaseController;
use Illuminate\Support\Facades\Auth;

class RatingController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.ratings'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        if (\request()->ajax()) {
            $ratings = Rating::with([
                'service' => function ($q) {
                    $q->withoutGlobalScope('company');
                },
                'deal' => function ($q) {
                    $q->withoutGlobalScope('company');
                },
                'product' => function ($q) {
                    $q->withoutGlobalScope('company');
                },
                'company'
            ])->withoutGlobalScope('company')->get();

            return \datatables()->of($ratings)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    $action .= '<a href="javascript:;" class="btn btn-primary btn-circle update-rating" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-rating" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';

                    $action .= '</div>';
                    return $action;
                })

                ->editColumn('item', function ($row) {

                    if(!is_null($row->service_id)){
                        return ucwords($row->service->name);
                    }
                    elseif(!is_null($row->deal_id)){
                        return ucwords($row->deal->name);
                    }
                    else{
                        return ucwords($row->product->name);
                    }
                })
                ->editColumn('company', function ($row) {
                    return ucwords($row->company->company_name);
                })
                ->editColumn('rating', function ($row) {
                    return $row->rating;
                })
                ->addColumn('status', function ($row) {
                    $selected = 'selected';

                    if ($row->status == 'active') {
                        return '<select class="custom-select feedback_status" data-feedback-id="' . $row->id . '">
                        <option value="active" ' . $selected . '>' . __('app.active') . '</option>
                        <option value="inactive">' . __('app.inactive') . '</option>
                        <option value="inreview" >' . __('app.InReview') . '</option>
                        </select>';
                    }
                    elseif ($row->status == 'inactive') {
                        return '<select class="custom-select feedback_status" data-feedback-id="' . $row->id . '">
                        <option value="active">' . __('app.active') . '</option>
                        <option value="inactive" ' . $selected . '>' . __('app.inactive') . '</option>
                        <option value="inreview" >' . __('app.InReview') . '</option>
                        </select>';
                    }
                    else {
                        return '<select class="custom-select feedback_status" data-feedback-id="' . $row->id . '">
                        <option value="active">' . __('app.active') . '</option>
                        <option value="inactive">' . __('app.inactive') . '</option>
                        <option value="inreview" ' . $selected . '>' . __('app.InReview') . '</option>
                        </select>';
                    }

                })

                ->addIndexColumn()
                ->rawColumns(['action', 'rating', 'status', 'code'])
                ->make(true);
        }

        return view('superadmin.ratings.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function edit(Rating $rating)
    {
        $ratings = Rating::with([
            'service' => function ($q) {
                $q->withoutGlobalScope('company');
            },
            'deal' => function ($q) {
                $q->withoutGlobalScope('company');
            },
            'product' => function ($q) {
                $q->withoutGlobalScope('company');
            }, 'company'
        ])->withoutGlobalScope('company')->findOrFail($rating->id);

        return view('superadmin.ratings.edit', compact('ratings'));
    }

    public function store(Request $request)
    {
        $ratingStatus = GlobalSetting::first();
        
        if ($request->rating_option == 'active') {
            $ratingStatus->rating_status = $request->rating_option;
        }
        else {
            $ratingStatus->rating_status = 'inactive';
        }

        $ratingStatus->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rating $rating)
    {
        $rating = Rating::findOrFail($rating->id);
        $rating->rating = $request->ratingValue;
        $rating->save();

        return Reply::dataOnly(['status' => 'success']);
    }

    public function changeStatus(Request $request, $id)
    {
        $rating = Rating::findOrFail($id);
        $rating->status = $request->status;
        $rating->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rating $rating)
    {
        $rating = Rating::findOrFail($rating->id);
        $rating->delete();

        return Reply::success(__('messages.feedbackDeletedSuccessfully'));
    }
    
}
