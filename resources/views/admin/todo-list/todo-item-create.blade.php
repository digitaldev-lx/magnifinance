<div class="modal-header">
    <h4 class="modal-title">@lang('modules.module.todos.createTodoItem')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form id="createTodoItem" class="ajax-form" method="POST" autocomplete="off" onkeydown="return event.key != 'Enter';">
        @csrf
        <div class="form-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>@lang('modules.module.todos.form.title')</label>

                        <input type="text" class="form-control form-control-lg" id="title" name="title" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="create-todo-item" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>
