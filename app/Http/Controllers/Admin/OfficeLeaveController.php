<?php

namespace App\Http\Controllers\Admin;

use App\OfficeLeave;
use App\Http\Controllers\AdminBaseController;
use App\Helper\Reply;
use App\Http\Requests\OfficeLeaves\StoreRequest;
use App\Http\Requests\OfficeLeaves\UpdateRequest;
use Carbon\Carbon;

class OfficeLeaveController extends AdminBaseController
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.office-leaves.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $office_Leave = new OfficeLeave();
        $office_Leave->title = $request->title;
        $office_Leave->start_date = $request->startDate;
        $office_Leave->end_date = $request->endDate;
        $office_Leave->save();

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
        $office_leave = OfficeLeave::where('id', $id)->firstOrFail();

        if (request()->ajax()) {
            return view('admin.office-leaves.edit', compact('office_leave'));
        }

        return view('admin.office-leaves.edit', compact('office_leave'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request,$id)
    {
        $office_Leave = OfficeLeave::where('id', $id)->firstOrFail();
        $office_Leave->title = $request->title;
        $office_Leave->start_date = $request->startDate;
        $office_Leave->end_date = $request->endDate;
        $office_Leave->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        OfficeLeave::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

}
