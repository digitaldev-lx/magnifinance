<div class="modal-header">
   <h4 class="modal-title">@lang('app.edit') @lang('app.signUpNote') </h4>
   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
   <form role="form" id="editForm" class="ajax-form" method="POST">
      @csrf
      <div class="row">
         <div class="col-md-12">
            <div class="form-group">
               <label>@lang('app.note')</label>
               <textarea name="sign_up_note" id="sign_up_note" cols="30" class="form-control-lg form-control"
                  rows="4">{!! $setting->sign_up_note !!}</textarea>
            </div>
         </div>
      </div>
   </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
        class="fa fa-check"></i> @lang('app.submit')</button>
</div>

<script>
    $('body').on('click', '#save-form', function() {
        const form = $('#editForm');

        $.easyAjax({
            url: '{{route('superadmin.updateNote', $setting->id)}}',
            container: '#editForm',
            type: "POST",
            redirect: true,
            data: form.serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });

    $(function () {
        $('#answer').summernote({
            dialogsInBody: true,
            height: 200,
            toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
        })
    });
</script>
