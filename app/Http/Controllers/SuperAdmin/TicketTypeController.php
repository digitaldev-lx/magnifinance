<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketType\Store;
use App\Models\TicketType;

;

class TicketTypeController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $ticketType = TicketType::get();

            return \datatables()->of($ticketType)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';
                    $action .= '<a href="javascript:;" class="btn btn-primary btn-circle edit-ticket-type" data-row-id="' . $row->id . '"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-ticket-type"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                        $action .= '</div>';

                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->toJson();
        }
    }

    public function create()
    {
        return view('superadmin.ticket-settings.ticket-type.create');
    }

    public function store(Store $request)
    {
        $ticketType = new TicketType();
        $ticketType->name = $request->name;
        $ticketType->save();

        return Reply::success( __('messages.createdSuccessfully'));
    }

    public function edit(TicketType $ticketType)
    {
        $this->ticketType = $ticketType;
        return view('superadmin.ticket-settings.ticket-type.edit', $this->data);
    }

    public function update(Store $request, TicketType $ticketType)
    {
        $ticketType->name = $request->name;
        $ticketType->update();

        return Reply::success( __('messages.updatedSuccessfully'));
    }

    public function destroy(TicketType $ticketType)
    {
        $ticketType->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

}
