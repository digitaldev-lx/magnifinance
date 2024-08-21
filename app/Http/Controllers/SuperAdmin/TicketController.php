<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Superadmin\Ticket\Store;
use App\Http\Requests\TicketReply\Store as ReplyStore;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketPriority;
use App\Models\TicketTemplate;
use App\Models\TicketType;
use App\User;
use Illuminate\Http\Request;

;

class TicketController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('app.ticket'));
    }

    public function index()
    {

         abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_ticket','create_ticket', 'update_ticket', 'delete_ticket']));

        if (\request()->ajax()) {
            $ticket = Ticket::query()->with(['vendor','agent', 'type', 'priority']);
            $ticket = $this->user->hasRole('superadmin') ? $ticket->get() : $ticket->agent($this->user->id)->get();

            return \datatables()->of($ticket)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_ticket')) {
                        $action .= '<a href="' . route('superadmin.tickets.edit', [$row->id]) . '" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_ticket')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('subject', function ($row) {
                    return $row->subject;
                })
                ->addColumn('vendor', function ($row) {
                    return $row->vendor->company_name ?? '--';
                })
                ->addColumn('agent', function ($row) {
                    return $row->agent->name ?? '--';
                })

                ->editColumn('status', function ($row) {
                    if ($row->status == 'open') {
                        return '<span class="badge badge-danger">' . __('app.open') . '</span>';
                    }
                    elseif ($row->status == 'pending') {
                        return '<label class="badge badge-warning">' . __('app.pending') . '</label>';
                    }
                    elseif ($row->status == 'resolved') {
                        return '<label class="badge badge-info">' . __('app.resolved') . '</label>';
                    }
                    elseif ($row->status == 'closed') {
                        return '<label class="badge badge-success">' . __('app.closed') . '</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->toJson();
        }

        return view('superadmin.ticket.index');
    }

    public function create()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_ticket'));

        $this->agents = User::allAgents()->get();
        $this->vendors = Company::withoutGlobalScopes()->get();
        $this->types = TicketType::get();
        $this->priorities = TicketPriority::get();
        $this->ticketTemplate = TicketTemplate::get();

        return view('superadmin.ticket.create', $this->data);
    }

    public function store(Store $request)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_ticket'));

        $ticket = new Ticket();
        $ticket->vendor_id = $request->vendor;

        if ($request->agent) {
            $ticket->agent_id = $request->agent;
        }
        elseif ($this->user->hasRole('agent')) {
            $ticket->agent_id = $this->user->id;
        }

        $ticket->type_id = $request->type;
        $ticket->priority_id = $request->priority;
        $ticket->subject = $request->subject;
        $ticket->status = $request->status;
        $ticket->save();

        $ticketComment = new TicketComment();
        $ticketComment->user_id = $this->user->id;
        $ticketComment->ticket_id = $ticket->id;
        $ticketComment->comment = $request->message;
        $ticketComment->save();

        return Reply::successWithData(__('messages.ticketReplySuccess'), ['ticketReplyID' => $ticketComment->id]);

    }

    public function replyStore(ReplyStore $request)
    {

        $ticket = Ticket::findOrFail($request->ticket_id);
        $ticket->status = $request->status;
        $ticket->update();

        $reply = new TicketComment();
        $reply->user_id = $this->user->id;
        $reply->ticket_id = $ticket->id;
        $reply->comment = $request->message;
        $reply->save();

        return Reply::dataOnly(['ticketReplyID' => $reply->id]);
    }

    public function latsReplyStore($id)
    {
        $reply = TicketComment::with('ticket')->findOrFail($id);
        $ticket = $reply->ticket;
        $lastMessage = view('superadmin.ticket.last-message', compact('reply', 'ticket'))->render();

        return Reply::successWithData(__('messages.ticketReplySuccess'), ['lastMessage' => $lastMessage]);
    }

    public function edit($id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_ticket'));

        $this->ticket = Ticket::with('reply')->findOrFail($id);
        $this->agents = User::allAgents()->get();
        $this->types = TicketType::get();
        $this->priorities = TicketPriority::get();
        $this->ticketTemplate = TicketTemplate::get();

        return view('superadmin.ticket.edit', $this->data);
    }

    public function update(Request $request, Ticket $ticket)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_ticket'));

        $ticket->agent_id = $request->agent;
        $ticket->type_id = $request->type;
        $ticket->priority_id = $request->priority;
        $ticket->update();

        return Reply::redirect(route('superadmin.tickets.index'), __('messages.updatedSuccessfully'));
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

    public function deleteReply($id)
    {
        $ticketComment = TicketComment::findOrFail($id);
        $ticketComment->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

    public function storeImages(Request $request)
    {
        if ($request->hasFile('file')) {
            $ticketComment = TicketComment::with('ticket')->where('id', $request->ticketReplyID)->first();

            if ($ticketComment) {
                $ticket_files_arr = [];

                foreach ($request->file as $fileData) {
                    array_push($ticket_files_arr, Files::uploadLocalOrS3($fileData, 'ticket/'.$ticketComment->ticket->id));
                }

                $ticketComment->files = json_encode($ticket_files_arr);
                $ticketComment->save();
            }
        }

        return Reply::redirect(route('superadmin.tickets.index'), __('messages.createdSuccessfully'));
    }

    public function fetchTemplate(Request $request)
    {
        $ticketTemplate = TicketTemplate::findOrFail($request->templateId);

        return Reply::dataOnly(['replyText' => $ticketTemplate->message]);
    }

}
