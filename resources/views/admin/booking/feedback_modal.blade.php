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

@if ($current_url !== 'bookingPage')
    <div class="modal-header">
        <h4 class="modal-title">@lang('modules.booking.feedback')</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
@endif

<div class="modal-body">
    <div class="row">
        <div class="col-md-12" id="invoices-list-panel">
            <div class="white-box">
                <div class="panel-body b-b">
                    <form role="form" id="ratingForm" class="ajax-form" method="POST">
                        @csrf
                        <input type="hidden" name="bookingId" id="bookingId" value="{{ $booking->id }}">
                        @foreach($booking->items as $key=>$item)
                        <div class="row">
                            @php
                                $item_name = '';
                                $item_type = '';
                                $item_id   = '';
                                if(!is_null($item->deal_id) && is_null($item->business_service_id) && is_null($item->product_id)) {
                                    $item_name = ucwords($item->deal->title);
                                    $item_type = 'deal';
                                    $item_id   = $item->deal_id;
                                }
                                else if(is_null($item->deal_id) && is_null($item->business_service_id) && !is_null($item->product_id)) {
                                    $item_name = $item->product->name;
                                    $item_type = 'product';
                                    $item_id   = $item->product_id;
                                }
                                else if(is_null($item->deal_id) && !is_null($item->business_service_id) && is_null($item->product_id)) {
                                    $item_name = ucwords($item->businessService->name);
                                    $item_type = 'service';
                                    $item_id   = $item->business_service_id;
                                }

                                $ratingId = $ratings->count() <= 0 ? 'store' : 'update';

                                $rating = 0;
                                if (!is_null($item->business_service_id))
                                {
                                    $rating = $item->ratingByUser?$item->ratingByUser->rating:0;
                                }
                            @endphp
                            <input type="hidden" name="ratingId" id="ratingId" value="{{ $ratingId }}">

                            <div class="col-md-12">
                                <div class='rating-stars'>
                                    <h5>{{ $item->businessService->name }}</h5>
                                    <input type="hidden" name="ratingValue[]" id="rating{{$item->id}}" value="{{$rating}}">
                                    <input type="hidden" name="itemId[]" value="{{ $item_id }}">
                                    <input type="hidden" name="itemType[]" value="{{ $item_type }}">
                                    <ul id='stars{{$item->id}}'>
                                        <li class='star @if($rating >= 1) selected @endif' title='Poor' data-value='1' data-item-id="{{$item->id}}">
                                            <i class='fa fa-star fa-fw'></i>
                                        </li>
                                        <li class='star @if($rating >= 2) selected @endif' title='Fair' data-value='2' data-item-id="{{$item->id}}">
                                            <i class='fa fa-star fa-fw'></i>
                                        </li>
                                        <li class='star @if($rating >= 3) selected @endif' title='Good' data-value='3' data-item-id="{{$item->id}}">
                                            <i class='fa fa-star fa-fw'></i>
                                        </li>
                                        <li class='star @if($rating >= 4) selected @endif' title='Excellent' data-value='4' data-item-id="{{$item->id}}">
                                            <i class='fa fa-star fa-fw'></i>
                                        </li>
                                        <li class='star @if($rating >= 5) selected @endif' title='WOW!!!' data-value='5' data-item-id="{{$item->id}}">
                                            <i class='fa fa-star fa-fw'></i>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div><br>
                        @endforeach

                        @if ($current_url == 'bookingPage')
                            <div class="col-md-12">
                                <div class="form-actions text-right">
                                    <button type="button" id="post-rating" class="btn btn-success"><i
                                        class="fa fa-check"></i>
                                        @lang('app.submit')
                                    </button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- .row -->

@if ($current_url == 'calendarPage')
<div class="modal-footer">
    <div class="col-md-12">
        <div class="form-actions text-right">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
                @lang('app.cancel')
            </button>
            <button type="button" id="post-rating" class="btn btn-success"><i
                    class="fa fa-check"></i>
                @lang('app.save')
            </button>
        </div>
    </div>
@endif

<script>

    ratingValue = 0;

    @foreach($booking->items as $key=>$itemDetail)
        /* 1. Visualizing things on Hover - See next part for action on click */
        var ids = {{ $itemDetail->id }};

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
    @endforeach

    $('#post-rating').click(function () {
        $.easyAjax({
            url: "{{route('admin.bookings.storeFeedback')}}",
            type: "POST",
            container: '#ratingForm',
            data: $('#ratingForm').serialize(),
            success: function (response) {
                location.reload();
            }
        });
    });
</script>
