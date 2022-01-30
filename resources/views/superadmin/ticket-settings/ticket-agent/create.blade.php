<div class="modal-header">
    <h4 class="modal-title">@lang('app.add') @lang('app.agent')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form class="form-horizontal ajax-form" id="ticket-agent-form" method="POST">
        @csrf
        <div class="row">
            <!-- text input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.name')</label>
                    <input type="text" class="form-control form-control-lg" name="name" value="" autocomplete="off">
                </div>
            </div>

            <!-- text input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.email')</label>
                    <input type="email" class="form-control form-control-lg" name="email" value="" autocomplete="off">
                </div>
            </div>

            <!-- text input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.password')</label>
                    <input type="password" class="form-control form-control-lg" name="password">
                </div>
            </div>

            <!-- text input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.mobile')</label>
                    <div class="form-row">
                        <div class="col-md-4 mb-2">
                            <select name="calling_code" id="calling_code" class="form-control select2">
                                @foreach ($calling_codes as $code => $value)
                                    <option value="{{ $value['dial_code'] }}">
                                        {{ $value['dial_code'] . ' - ' . $value['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-lg" name="mobile">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" onclick="saveTicketAgent()" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>


<script>
    $(".select2").select2({
        dropdownParent: $("#myModalDefault")
    });

    function saveTicketAgent() {
        $.easyAjax({
            url: '{{ route('superadmin.ticket-agents.store') }}',
            container: '#ticket-agent-form',
            type: "POST",
            data: $('#ticket-agent-form').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    $.unblockUI();
                    $(modal_default).modal('hide');
                    ticketAgentTable._fnDraw();
                }
            }
        })
    }
</script>
