<div class="mt-4 d-flex flex-wrap">
    @foreach ($articles as $article)
        <div class="col-md-6 mobile-no-padding">
            <div class="card single_deal_box border-0">
                <div class="card-image position-relative">
                    <a class="m-auto" href="{{route('front.blog.article', $article->slug)}}">
                    <img class="card-img-top" src="{{asset('front/images/pixel.gif')}}" data-src="{{ $article->article_image_url }}" alt="Card image"></a>
                    {{--@if($article->percentage > 0)
                    <span>
                        @if($article->discount_type == 'percentage')
                            {{$article->percentage}}%
                        @else
                        {{currencyFormatter($article->converted_original_amount - $article->converted_deal_amount)}}
                        @endif
                        @lang('app.off')
                    </span>
                    @endif--}}
                </div>
                <div class="card-body all_deals_services">
                    <h4 class="card-title" title="{{ $article->title }}">{{ $article->limit_title }}</h4>
                    <p class="card-text">
                        @php($author = is_null($article->company_id) ? 'SpotB' : $article->company->company_name)
                        <span class="mt-0"><b>{{ $author }}</b></span><br>
                        <span class="mt-0">{{ $article->excerpt}}</span>
                    </p>
                    <a
                        id="deal{{ $article->id }}"
                        href="{{route('front.blog.article', $article->slug)}}"
                        class="btn w-100 add-to-cart">
                        {{__('front.readMore')}}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="deals_pagination mt-4 d-flex justify-content-center" id="pagination">
    {{ $articles->links() }}
</div>
