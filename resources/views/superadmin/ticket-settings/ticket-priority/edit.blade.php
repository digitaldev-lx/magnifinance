<div class="modal-header">
    <h4 class="modal-title">@lang('app.edit') @lang('app.priority')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form class="form-horizontal ajax-form" id="update-ticket-priority-form" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- text input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.name')</label>
                    <input type="text" class="form-control form-control-lg" name="name" value="{{$ticketPriority->name}}" autocomplete="off">
                </div>
            </div>

        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" onclick="updateTicketPriority()" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>


<script>
    $(".select2").select2({
        dropdownParent: $("#myModalDefault")
    });

    function updateTicketPriority() {
        $.easyAjax({
            url: '{{ route('superadmin.ticket-priorities.update', $ticketPriority->id) }}',
            container: '#update-ticket-priority-form',
            type: "POST",
            data: $('#update-ticket-priority-form').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    $.unblockUI();
                    $(modal_default).modal('hide');
                    ticketPriorityTable._fnDraw();
                }
            }
        })
    }
</script>
