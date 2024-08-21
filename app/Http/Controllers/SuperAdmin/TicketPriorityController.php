<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketPriority\Store;
use App\Models\TicketPriority;

;

class TicketPriorityController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $ticketPriority = TicketPriority::get();

            return \datatables()->of($ticketPriority)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';
                    $action .= '<a href="javascript:;" class="btn btn-primary btn-circle edit-ticket-priority" data-row-id="' . $row->id . '"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-ticket-priority"
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
        return view('superadmin.ticket-settings.ticket-priority.create');
    }

    public function store(Store $request)
    {
        $ticketPriority = new TicketPriority();
        $ticketPriority->name = $request->name;
        $ticketPriority->save();

        return Reply::success( __('messages.createdSuccessfully'));
    }

    public function edit(TicketPriority $ticketPriority)
    {
        $this->ticketPriority = $ticketPriority;
        return view('superadmin.ticket-settings.ticket-priority.edit', $this->data);
    }

    public function update(Store $request, TicketPriority $ticketPriority)
    {
        $ticketPriority->name = $request->name;
        $ticketPriority->update();

        return Reply::success( __('messages.updatedSuccessfully'));
    }

    public function destroy(TicketPriority $ticketPriority)
    {
        $ticketPriority->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

}
