<div class="modal-header">
    <h5 class="modal-title">@lang('app.edit') @lang('app.page')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form role="form" id="editPageForm"  class="ajax-form" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $page->id }}">
        <div class="row">
            <div class="col-md">
                <div class="form-group">
                    <label>@lang('app.page') @lang('app.title')</label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg" value="{{ $page->title }}" autofocus>
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    <label>@lang('app.page') @lang('app.slug')</label>
                    <input type="text" name="slug" id="slug" class="form-control form-control-lg" value="{{ $page->slug }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.page') @lang('app.content')</label>
                    <textarea name="content" id="content" cols="30" class="form-control-lg form-control" rows="4">{{ $page->content }}</textarea>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="updatePageForm" class="btn btn-success btn-light-round" data-row-id="{{ $page->slug }}"><i
        class="fa fa-check"></i> @lang('app.submit')</button>
</div>

<script>
    $(function() {
        $('#content').summernote({
            dialogsInBody: true,
            height: 300,
            toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
        })
    })



    function createSlug(value) {
        value = value.replace(/\s\s+/g, ' ');
        let slug = value.split(' ').join('-').toLowerCase();
        slug = slug.replace(/--+/g, '-');
        slug = slug.replace(/%+/g, '-');
        $('#slug').val(slug);
    }

    $(document).on('keyup', '#title', function () {
        createSlug($(this).val());
    });

    $(document).on('keyup', '#slug', function () {
        createSlug($(this).val());
    });
</script>
