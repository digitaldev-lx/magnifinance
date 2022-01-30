<div class="modal-header">
    <h5 class="modal-title">@lang('app.createNew') @lang('app.page')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form role="form" id="createPageForm" class="ajax-form" method="POST">
        @csrf
        <div class="row">
            <div class="col-md">
                <!-- text input -->
                <div class="form-group">
                    <label>@lang('app.page') @lang('app.title')</label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg" value="">
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    <label>@lang('app.page') @lang('app.slug')</label>
                    <input type="text" name="slug" id="slug" class="form-control form-control-lg" value="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.page') @lang('app.content')</label>
                    <textarea name="content" id="content" cols="30" class="form-control-lg form-control"
                        rows="4"></textarea>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="savePageForm" class="btn btn-success btn-light-round"><i
        class="fa fa-check"></i> @lang('app.submit')</button>
</div>

<script>
    $(function () {
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
