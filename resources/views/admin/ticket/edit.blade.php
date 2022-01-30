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
                        <div class="col-12  card-body @if ($reply->user->id == $user->id) bg-owner-reply
                        @else bg-other-reply @endif "
                            id="replyMessageBox_{{ $reply->id }}">

                            <div class="row">
                                <div class="col-xs-2 col-md-1">
                                    <img src="{{ $reply->user->user_image_url }}" alt="user" class="rounded-circle"
                                        width="40" height="40">
                                </div>
                                <div class="col-xs-10 col-md-11">
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
                                                                class="fa fa-search text-light"></i></a>


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
                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                                class="fa fa-check"></i> @lang('app.submit')</button>
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
            url: "{{ route('admin.tickets.storeImages') }}",
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

        $('body').on('click', '#save-form', function() {

            $.easyAjax({
                url: '{{route('admin.tickets.reply')}}',
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
            var url = "{{ route('admin.tickets.latsReply',':id') }}";
            url = url.replace(':id', ticketReplyID);

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
            var url = "{{ route('admin.tickets.fetch_template') }}";
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
    </script>

@endpush
