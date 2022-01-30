@extends('layouts.master')
@push('head-css')
    <style>
        .select2-container--default .select2-selection--single{height: calc(2.875rem + 2px) !important;}
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height: 2.7 !important;}
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: calc(2.875rem + -2px) !important; }

        #ticket-messages {
            max-height: 550px;
            overflow-y: auto;
        }

        .dropdown-menu>li>a {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: 400;
            line-height: 1.42857143;
            color: #333;
            white-space: nowrap;
        }
        .btn-circle {
            width: 15px;
            height: 15px;
            padding: 6px 0;
            border-radius: 15px;
            text-align: center;
            font-size: 12px;
            line-height: 1.428571429;
        }
        .bg-other-reply {
            background: #f6fbff;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .bg-owner-reply {
            background: #f6f7f9;
            margin-bottom: 10px;
            border-radius: 6px;
        }
    </style>
@endpush
@section('content')
@if ($user->hasRole('superadmin'))
<div class="row">
    <div class="col-md-12">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">@lang('app.update') @lang('app.ticket') - {{ $ticket->subject }}</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <form role="form" id="createForm" class="ajax-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.agent')</label>
                                <div class="input-group">
                                    <select name="agent" id="agent" class="form-control form-control-lg select2">
                                        <option value="">@lang('app.select') @lang('app.agent')</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}"
                                                {{ $agent->id == $ticket->agent_id ? 'selected' : '' }}>
                                                {{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.type')</label>
                                <div class="input-group">
                                    <select name="type" id="type" class="form-control form-control-lg">
                                        <option value="">@lang('app.select') @lang('app.type')</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $type->id == $ticket->type_id ? 'selected' : '' }}>
                                                {{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.priority')</label>
                                <div class="input-group">
                                    <select name="priority" id="priority" class="form-control form-control-lg">
                                        <option value="">@lang('app.select') @lang('app.priority')</option>
                                        @foreach ($priorities as $priority)
                                            <option value="{{ $priority->id }}"
                                                {{ $priority->id == $ticket->priority_id ? 'selected' : '' }}>
                                                {{ $priority->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">

                            <div class="form-group">
                                <button type="button" id="save-form" class="btn btn-success btn-light-round">
                                    <i class="fa fa-check"></i> @lang('app.save')</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">{{ $ticket->subject }}
                    <span id="ticket-status" class="m-r-5">
                        <label class="badge
                                @if($ticket->status == 'open')
                                    badge-danger
                                @elseif($ticket->status == 'pending')
                                    badge-warning
                                @elseif($ticket->status == 'resolved')
                                    badge-info
                                @elseif($ticket->status == 'closed')
                                    badge-success
                                @endif ">
                            {{ $ticket->status }}</label>
                    </span>
                </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <div class="row" id="ticket-messages">

                    @forelse($ticket->reply as $reply)
                        <div class="col-12 card-body @if ($reply->user->id == $user->id) bg-owner-reply
                        @else bg-other-reply @endif "
                            id="replyMessageBox_{{ $reply->id }}">

                            <div class="row">

                                <div class="col-xs-2 col-md-1">
                                    <img src="{{ $reply->user->user_image_url }}" alt="user" class="rounded-circle"
                                        width="40" height="40">
                                </div>
                                <div class="col-xs-8 col-md-10">
                                    <h5 class="m-t-0 font-bold">
                                        <div class="text-dark">{{ ucwords($reply->user->name) }}
                                            <span
                                                class="text-muted text-sm font-weight-normal">{{ $reply->created_at->timezone($settings->timezone)->format($settings->date_format . ' ' . $settings->time_format) }}</span>
                                        </div>
                                    </h5>

                                    <div class="font-light">
                                        {!! ucfirst(nl2br($reply->comment)) !!}
                                    </div>
                                </div>

                                <div class="col-xs-2 col-md-1">
                                    <a href="javascript:;" data-toggle="tooltip" data-placement='left'
                                        data-original-title="@lang('app.delete')" data-reply-id="{{ $reply->id }}"
                                        class="btn btn-outline-dark sa-params" data-pk="list"><i
                                            class="fa fa-trash"></i></a>
                                </div>

                            </div>
                            @if ($reply->files)
                                <div class="col-12" id="list">
                                    <ul class="list-group" id="files-list">
                                        @forelse($reply->files as $file)
                                            <li class="list-group-item col-md-12">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        {{ $file }}
                                                    </div>
                                                    <div class="col-md-4 text-right">

                                                        <a target="_blank"
                                                            href="{{ asset_url('ticket/' . $ticket->id . '/' . $file) }}"
                                                            data-toggle="tooltip" data-original-title="@lang('app.view')"
                                                            class="btn btn-outline-info"><i
                                                                class="fa fa-search"></i></a>


                                                        &nbsp;&nbsp;
                                                        <a href="{{ asset_url('ticket/' . $ticket->id . '/' . $file) }}"
                                                            download data-toggle="tooltip"
                                                            data-original-title="@lang('app.download')"
                                                            class="btn btn-outline-dark"><i
                                                                class="fa fa-download"></i></a>

                                                    </div>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        @lang('messages.noFileUploaded')
                                                    </div>
                                                </div>
                                            </li>
                                        @endforelse

                                    </ul>
                                </div>
                                <!--/row-->
                            @endif
                        </div>
                        @empty
                            <div class="card-body b-b">
                                <div class="row">
                                    <div class="col-md-12">
                                        @lang('messages.noMessage')
                                    </div>
                                </div>
                                <!--/row-->
                            </div>
                        @endforelse
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <button class="btn btn-secondary btn-sm waves-effect waves-light" id="reply-toggle"
                            type="button"><i class="fa fa-mail-reply"></i> @lang('app.reply')
                        </button>
                    </div>
                </div>
                <div class="row mt-2" id="reply-section" style="display: none;">
                    <div class="col-12">
                        <form role="form" id="replyForm" class="ajax-form" method="POST">
                            @csrf
                            <input type="hidden" name="status" id="status" value="{{$ticket->status}}">
                            <input type="hidden" name="ticket_id" value="{{$ticket->id}}">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.message')</label>
                                    <textarea name="message" id="message" cols="30"
                                        class="form-control-lg form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-form-label"> @lang('app.files')</label>
                                <div id="file-upload-box">
                                    <div class="row" id="file-dropzone">
                                        <div class="col-md-12">
                                            <div class="dropzone" id="ticket-file-upload-dropzone">
                                                {{ csrf_field() }}
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-2 text-right">
                                <div class="btn-group dropup mr-1">
                                    <button aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" id="replyTemplate"
                                        class="btn btn-outline-info dropdown-toggle waves-effect waves-light"
                                        type="button"><i class="fa fa-bolt"></i> @lang('modules.tickets.applyTemplate')
                                        <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu" aria-labelledby="replyTemplate">
                                        @forelse ($ticketTemplate as $template)
                                            <li class="dropdown-item"><a href="javascript:;" data-template-id="{{ $template->id }}"
                                                    class="apply-template">{{ $template->name }}</a></li>
                                        @empty
                                            <li class="dropdown-item">@lang('messages.noTemplateFound')</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="btn-group dropup">
                                    <button aria-expanded="true" data-toggle="dropdown"
                                        class="btn btn-success dropdown-toggle waves-effect waves-light" id="replySubmit"
                                        type="button">@lang('app.submit') <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu float-right" aria-labelledby="replySubmit">
                                        <li class="dropdown-item">
                                            <a href="javascript:;" class="submit-ticket"
                                                data-status="open">@lang('modules.tickets.submitOpen')
                                                <span style="width: 15px; height: 15px;"
                                                    class="btn btn-danger btn-small btn-circle">&nbsp;</span>
                                            </a>
                                        </li>
                                        <li class="dropdown-item">
                                            <a href="javascript:;" class="submit-ticket"
                                                data-status="pending">@lang('modules.tickets.submitPending')
                                                <span style="width: 15px; height: 15px;"
                                                    class="btn btn-warning btn-small btn-circle">&nbsp;</span>
                                            </a>
                                        </li>
                                        <li class="dropdown-item">
                                            <a href="javascript:;" class="submit-ticket"
                                                data-status="resolved">@lang('modules.tickets.submitResolved')
                                                <span style="width: 15px; height: 15px;"
                                                    class="btn btn-info btn-small btn-circle">&nbsp;</span>
                                            </a>
                                        </li>
                                        <li class="dropdown-item">
                                            <a href="javascript:;" class="submit-ticket"
                                                data-status="closed">@lang('modules.tickets.submitClosed')
                                                <span style="width: 15px; height: 15px;"
                                                    class="btn btn-success btn-small btn-circle">&nbsp;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                    <!-- /.card-body -->
            </div>
                <!-- /.card -->
        </div>
    </div>
</div>



@endsection

@push('footer-js')

    <script>

        $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        })

        var ticketReplyID;
        var lastIndex = 0;
        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("#ticket-file-upload-dropzone", {
            url: "{{ route('superadmin.tickets.storeImages') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            init: function () {
                myDropzone = this;
            },
            dictDefaultMessage: "@lang('app.dropzone.defaultMessage')",
            dictRemoveFile: "@lang('app.dropzone.removeFile')"
        });

        myDropzone.on('sending', function(file, xhr, formData) {
            formData.append('ticketReplyID', ticketReplyID);
        });

        myDropzone.on('completemultiple', function () {
            getLastReply();
            myDropzone.removeAllFiles();
        });

        $('body').on('click', '.submit-ticket', function() {
            var status = $(this).data('status');
            $('#status').val(status);

            $.easyAjax({
                url: '{{route('superadmin.tickets.reply')}}',
                container: '#replyForm',
                type: "POST",
                data: $('#replyForm').serialize(),
                success: function (response) {

                    ticketReplyID = response.ticketReplyID;
                    if (myDropzone.getQueuedFiles().length > 0) {
                        myDropzone.processQueue();
                    }
                    else{
                        getLastReply();
                    }

                }
            })
        });
        function getLastReply() {
            var url = "{{ route('superadmin.tickets.latsReply',':id') }}";
            url = url.replace(':id', ticketReplyID);
            var status = $('#status').val();

            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if(response.status == 'success'){
                        if(response.lastMessage != null){
                            console.log('yes1');
                            $('#ticket-messages').append(response.lastMessage);
                        }
                        $('#message').summernote('code', '');

                        // update status on top
                        if(status == 'open')
                            $('#ticket-status').html('<label class="badge badge-danger">@lang("app.open")</label>');
                        else if(status == 'pending')
                            $('#ticket-status').html('<label class="badge badge-warning">@lang("app.pending")</label>');
                        else if(status == 'resolved')
                            $('#ticket-status').html('<label class="badge badge-info">@lang("app.resolved")</label>');
                        else if(status == 'closed')
                            $('#ticket-status').html('<label class="badge badge-success">@lang("app.closed")</label>');

                        $.unblockUI();
                        scrollChat();
                        replyToggle();
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                }
            })
        }
        function scrollChat() {
            $('#ticket-messages').animate({
                scrollTop: $('#ticket-messages')[0].scrollHeight
            }, 'slow');
        }


        $('body').on('click', '#save-form', function() {
            $.easyAjax({
                url: '{{route('superadmin.tickets.update', $ticket->id)}}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file:true,
                data: $('#createForm').serialize()
            })
        });

        $('body').on('click', '#reply-toggle', function() {
            replyToggle();
        });

        function replyToggle() {
            $('#reply-toggle').toggle();
            $('#reply-section').toggle();
        }


        $(function () {
                    $('#message').summernote({
                        dialogsInBody: true,
                        height: 300,
                        toolbar: [
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['strikethrough']],
                            ['fontsize', ['fontsize']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ["view", ["fullscreen"]]
                        ]
                    });
        });


        $('body').on('click', '.apply-template', function() {
            var templateId = $(this).data('template-id');
            var url = "{{ route('superadmin.tickets.fetch_template') }}";
            var token = "{{ csrf_token() }}";

            $.easyAjax({
                url: url,
                type: "POST",
                data: { _token: token, templateId: templateId },
                success: function (response) {
                    $('#message').summernote('code', response.replyText);
                }
            })
        });

        $('body').on('click', '.sa-params', function(){
                var id = $(this).data('reply-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('superadmin.tickets.reply.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    $('#replyMessageBox_'+id).fadeOut();
                                }
                            }
                        });
                    }
                });
            });
    </script>

@endpush
