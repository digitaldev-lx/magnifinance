<style>
    /* Rating Star Widgets Style */
    .rating-stars ul {
        list-style-type: none;
        padding: 0;

        -moz-user-select: none;
        -webkit-user-select: none;
    }

    .rating-stars ul>li.star {
        display: inline-block;
        margin: 1px;

    }

    /* Idle State of the stars */
    .rating-stars ul>li.star>i.fa {
        font-size: 1.6em;
        /* Change the size of the stars */
        color: #ccc;
        /* Color on idle state */
    }

    /* Hover state of the stars */
    .rating-stars ul>li.star.hover>i.fa {
        color: #FFCC36;
    }

    /* Selected state of the stars */
    .rating-stars ul>li.star.selected>i.fa {
        color: #FF912C;
    }

    .d-none {
        display: none;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title">@lang('modules.booking.editFeedback')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form id="editFeedback" class="ajax-form" method="POST" autocomplete="off" onkeydown="return event.key != 'Enter';">
        @csrf
        @method('PUT')
        <div class="form-body">
            <div class="row">
                <div class="col-md-12" id="invoices-list-panel">
                    <div class="white-box">
                        <div class="panel-body b-b">
                            @if($ratings->count() >= 0)
                                <form role="form" id="ratingForm" class="ajax-form" method="POST">
                                    @csrf
                                    <input type="hidden" name="ratingId" id="ratingId" value="{{ $ratings->id }}">
                                    <div class="row">
                                        @php
                                            $item_name = '';
                                            $item_type = '';
                                            $item_id   = '';
                                            if(!is_null($ratings->deal_id) && is_null($ratings->service_id) && is_null($ratings->product_id)) {
                                                $item_name = ucwords($ratings->deal->name);
                                                $item_type = 'deal';
                                                $item_id   = $ratings->deal_id;
                                            }
                                            else if(is_null($ratings->deal_id) && is_null($ratings->service_id) && !is_null($ratings->product_id)) {
                                                $item_name = $ratings->product->name;
                                                $item_type = 'product';
                                                $item_id   = $ratings->product_id;
                                            }
                                            else if(is_null($ratings->deal_id) && !is_null($ratings->service_id) && is_null($ratings->product_id)) {
                                                $item_name = ucwords($ratings->service->name);
                                                $item_type = 'service';
                                                $item_id   = $ratings->service_id;
                                            }
        
                                        @endphp
                                        <div class="col-md-12">
                                            <div class='rating-stars'>
                                                <h5>{!! $item_name !!}</h5>
                                                <input type="hidden" name="ratingValue" id="rating{{$ratings->id}}">
                                                <input type="hidden" name="itemId" value="{{ $item_id }}">
                                                <input type="hidden" name="itemType" value="{{ $item_type }}">
                                                <ul id='stars{{$ratings->id}}'>
                                                    <li class='star @if($ratings->rating >= 1) selected @endif' title='Poor' data-value='1' data-item-id="{{$ratings->id}}">
                                                        <i class='fa fa-star fa-fw'></i>
                                                    </li>
                                                    <li class='star @if($ratings->rating >= 2) selected @endif' title='Fair' data-value='2' data-item-id="{{$ratings->id}}">
                                                        <i class='fa fa-star fa-fw'></i>
                                                    </li>
                                                    <li class='star @if($ratings->rating >= 3) selected @endif' title='Good' data-value='3' data-item-id="{{$ratings->id}}">
                                                        <i class='fa fa-star fa-fw'></i>
                                                    </li>
                                                    <li class='star  @if($ratings->rating >= 4) selected @endif' title='Excellent' data-value='4' data-item-id="{{$ratings->id}}">
                                                        <i class='fa fa-star fa-fw'></i>
                                                    </li>
                                                    <li class='star  @if($ratings->rating >= 5) selected @endif' title='WOW!!!' data-value='5' data-item-id="{{$ratings->id}}">
                                                        <i class='fa fa-star fa-fw'></i>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif
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
    <button type="button" id="update-feedback" data-id="{{ $ratings->id }}" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>

<script>

    ratingValue = 0;

    /* 1. Visualizing things on Hover - See next part for action on click */
    var ids = {{ $ratings->id }};

    $('#stars'+ids+' li').on('mouseover', function(){
        var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

        // Now highlight all the stars that's not after the current hovered star
        $(this).parent().children('li.star').each(function(e){
            if (e < onStar) {
                $(this).addClass('hover');
            }
            else {
                $(this).removeClass('hover');
            }
        });

    }).on('mouseout', function(){
        $(this).parent().children('li.star').each(function(e){
            $(this).removeClass('hover');
        });
    });

    /* 2. Action to perform on click */
    $('#stars'+ids+' li').on('click', function(){
        var onStar = parseInt($(this).data('value'), 10); // The star currently selected
        var stars = $(this).parent().children('li.star');

        for (i = 0; i < stars.length; i++) {
            $(stars[i]).removeClass('selected');
        }

        for (i = 0; i < onStar; i++) {
            $(stars[i]).addClass('selected');
        }
        
        var ratingValue = parseInt($(this).data('value'), 10);
        var ratingId = $(this).data('item-id');

        $('#rating'+ratingId).val(ratingValue);
    });
</script>