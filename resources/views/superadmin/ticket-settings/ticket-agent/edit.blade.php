<div class="modal-header">
    <h4 class="modal-title">@lang('app.edit') @lang('app.agent')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form class="form-horizontal ajax-form" id="update-ticket-agent-form" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- text input -->
            <input type="hidden" name="id" value="{{$agentUser->id}}">
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.name')</label>
                    <input type="text" class="form-control form-control-lg" name="name" value="{{$agentUser->name}}" autocomplete="off">
                </div>
            </div>

            <!-- text input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.email')</label>
                    <input type="email" class="form-control form-control-lg" name="email" value="{{$agentUser->email}}" autocomplete="off">
                </div>
            </div>

            <!-- text input -->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.password')</label>
                    <input type="password" class="form-control form-control-lg" name="password">
                    <span class="help-block">@lang('messages.leaveBlank')</span>
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
    <button type="button" onclick="updateTicketAgent()" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>


<script>
    $('.select2').select2();
    function updateTicketAgent() {
        $.easyAjax({
            url: '{{ route('superadmin.ticket-agents.update', $agentUser->id) }}',
            container: '#update-ticket-agent-form',
            type: "POST",
            data: $('#update-ticket-agent-form').serialize(),
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
