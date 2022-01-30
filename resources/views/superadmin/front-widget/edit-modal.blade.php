<div class="modal-header">
    <h5 class="modal-title">@lang('app.edit') @lang('app.front.widget.title') </h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
 </div>
 <div class="modal-body">
    <form role="form" id="editWidgetForm" class="ajax-form" method="POST">
       @csrf
       @method('PUT')
       <div class="row">

          <div class="col-md-12">
             <!-- text input -->
             <div class="form-group">
                <label>@lang('app.frontwidget.name')</label>
                <input type="text" name="name" id="name" class="form-control form-control-lg" value="{{ $widgets->name }}">
             </div>
          </div>
          <div class="col-md-12">
             <div class="form-group">
                <label>@lang('app.frontwidget.code')</label>
                <textarea name="code" id="code" cols="30" class="form-control-lg form-control"
                   rows="4">{{$widgets->code }}</textarea>
             </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
                <label>@lang('app.frontwidget.status')</label>
                <select name="status" id="status" class="form-control form-control-lg">

                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>

                </select>
            </div>
       </div>
    </form>
 </div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="updateWidgetForm" data-row-id="{{ $widgets->id }}" class="btn btn-success btn-light-round"><i
        class="fa fa-check"></i> @lang('app.submit')</button>
</div>
