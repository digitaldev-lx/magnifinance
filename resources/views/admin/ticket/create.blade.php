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

    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.add') @lang('app.ticket')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm"  class="ajax-form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.type')</label>
                                    <div class="input-group">
                                        <select name="type" id="type" class="form-control form-control-lg">
                                            <option value="">@lang('app.select') @lang('app.type')</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}" >{{ $type->name }}</option>
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
                                            @foreach($priorities as $priority)
                                                <option value="{{ $priority->id }}" >{{ $priority->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.subject')</label>
                                    <input type="text" name="subject" id="subject" class="form-control form-control-lg" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.message')</label>
                                    <textarea name="message" id="message" cols="30" class="form-control-lg form-control" rows="4"></textarea>
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

                        </div>

                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection

@push('footer-js')

    <script>


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
            window.location.href = '{{ route('admin.tickets.index') }}'
        });

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


        $('body').on('click', '#save-form', function() {
            $.easyAjax({
                url: '{{route('admin.tickets.store')}}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file:true,
                data: $('#createForm').serialize(),
                success: function (response) {
                    if (myDropzone.getQueuedFiles().length > 0) {
                        ticketReplyID = response.ticketReplyID;
                        myDropzone.processQueue();
                    }
                    else{
                            setTimeout(function()
                            {
                                window.location.href = '{{ route('admin.tickets.index') }}'
                                return false;
                            }, 1000);
                    }
                }
            })
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
