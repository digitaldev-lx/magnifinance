<div class="modal-header">
    <h4 class="modal-title">@lang('app.edit') @lang('app.template')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form class="form-horizontal ajax-form" id="update-ticket-template-form" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- text input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.name')</label>
                    <input type="text" class="form-control form-control-lg" name="name" value="{{$ticketTemplate->name}}" autocomplete="off">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">@lang('app.message')</label>
                    <textarea name="message" id="message" cols="30" class="form-control-lg form-control" rows="4">{{$ticketTemplate->message}}</textarea>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" onclick="updateTicketTemplate()" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>


<script>

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
    function updateTicketTemplate() {
        $.easyAjax({
            url: '{{ route('superadmin.ticket-templates.update', $ticketTemplate->id) }}',
            container: '#update-ticket-template-form',
            type: "POST",
            data: $('#update-ticket-template-form').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    $.unblockUI();
                    $(modal_default).modal('hide');
                    ticketTemplateTable._fnDraw();
                }
            }
        })
    }
</script>
