<div class="modal-header">
   <h4 class="modal-title">@lang('app.edit') @lang('app.faq.title') </h4>
   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
   <form role="form" id="editFaqForm" class="ajax-form" method="POST">
      @csrf
      @method('PUT')
      <div class="row">
         <div class="col-md-12">
            <div class="form-group">
               <label>@lang('app.language')</label>
               <select name="language" id="lang-status" class="form-control form-control-lg">
               @foreach ($languages as $language)
               <option {{ $faq->language_id == $language->id ? 'selected' : '' }}  value="{{ $language->id }}">{{ $language->language_name }}</option>
               @endforeach
               </select>
            </div>
         </div>
         <div class="col-md-12">
            <!-- text input -->
            <div class="form-group">
               <label>@lang('app.faq.question')</label>
               <input type="text" name="question" id="question" class="form-control form-control-lg" value="{{ $faq->question }}">
            </div>
         </div>
         <div class="col-md-12">
            <div class="form-group">
               <label>@lang('app.faq.answer')</label>
               <textarea name="answer" id="answer" cols="30" class="form-control-lg form-control"
                  rows="4">{!! $faq->answer !!}</textarea>
            </div>
         </div>
      </div>
   </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="updateFaqForm" data-row-id="{{ $faq->id }}" class="btn btn-success btn-light-round"><i
        class="fa fa-check"></i> @lang('app.submit')</button>
</div>

<script>


    $(function () {
        $('#answer').summernote({
            dialogsInBody: true,
            height: 200,
            toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
        })
    });
</script>
