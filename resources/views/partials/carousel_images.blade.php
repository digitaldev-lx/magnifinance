@foreach($images as $image)
    <div class="col-md-4">
        <div class="card">
            <div class="img-holder">
                <img class="card-img-top img-fluid" src="{{ asset('user-uploads/carousel-images/'.$image->file_name) }}" alt="carousel-image">
            </div>
            <div class="card-body">
                <button id="{{ $image->id }}" class="btn btn-danger pull-right delete-carousel-row">
                    @lang('app.delete')
                </button>
            </div>
        </div>
    </div>
@endforeach
