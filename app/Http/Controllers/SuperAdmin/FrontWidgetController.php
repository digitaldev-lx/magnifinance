<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Language;
use App\FrontWidget;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use Illuminate\Http\Request;

class FrontWidgetController extends SuperAdminBaseController
{

    /**
     * index
     *
     * @return mixed
     */
    public function index()
    {
        if (request()->ajax()) {
            $frontWidgets = FrontWidget::all();

            return datatables()->of($frontWidgets)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    $action .= '<a href="javascript:;" data-widget-id="' . $row->id . '" class="btn btn-primary btn-circle edit-widget"
                      data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-widget"
                          data-toggle="tooltip" data-widget-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->editColumn('status', function ($row) {

                    return ucfirst($row->status);
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->toJson();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.front-widget.create-modal');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $frontWidgets = new FrontWidget();

        $frontWidgets->name = $request->name;
        $frontWidgets->code = $request->code;
        $frontWidgets->status = $request->status;

        $frontWidgets->save();

        return Reply::success(__('messages.frontwidget.addedSuccess'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->widgets = FrontWidget::find($id);
        return view('superadmin.front-widget.edit-modal', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $frontwidget = FrontWidget::findOrFail($id);
        $frontwidget->name = $request->name;
        $frontwidget->code = $request->code;
        $frontwidget->status = $request->status;


        $frontwidget->save();

        return Reply::success(__('messages.frontwidget.updatedSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FrontWidget::where('id', $id)->delete();
        return Reply::success(__('messages.frontwidget.deletedSuccess'));
    }

}
