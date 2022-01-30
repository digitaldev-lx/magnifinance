<?php

namespace App\Http\Controllers\SuperAdmin;

use App\TicketTemplate;
use Illuminate\Http\Request;
use App\Helper\Reply;
;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketTemplate\Store;

class TicketTemplateController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $ticketTemplate = TicketTemplate::get();

            return \datatables()->of($ticketTemplate)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';
                    $action .= '<a href="javascript:;" class="btn btn-primary btn-circle edit-ticket-template" data-row-id="' . $row->id . '"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-ticket-template"
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
        return view('superadmin.ticket-settings.ticket-template.create');
    }

    public function store(Store $request)
    {
        $ticketTemplate = new TicketTemplate();
        $ticketTemplate->name = $request->name;
        $ticketTemplate->message = $request->message;
        $ticketTemplate->save();

        return Reply::success( __('messages.createdSuccessfully'));
    }

    public function edit(TicketTemplate $ticketTemplate)
    {
        $this->ticketTemplate = $ticketTemplate;
        return view('superadmin.ticket-settings.ticket-template.edit', $this->data);
    }

    public function update(Store $request, TicketTemplate $ticketTemplate)
    {
        $ticketTemplate->name = $request->name;
        $ticketTemplate->message = $request->message;
        $ticketTemplate->update();

        return Reply::success( __('messages.updatedSuccessfully'));
    }

    public function destroy(TicketTemplate $ticketTemplate)
    {
        $ticketTemplate->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

}
