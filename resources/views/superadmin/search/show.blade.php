@extends('layouts.master')
@push('head-css')
<style>
#pagination ul li {
    border-bottom: none;
}
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div id="search-page" class="card-body">
                    <h3 class="box-title">@lang('modules.search.searchHere')</h3>
                    <form class="form-group" action="{{ route('superadmin.search.store') }}" novalidate method="POST" role="search">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="input-group">
                            <input type="text"  name="search_key" class="form-control" placeholder="@lang('modules.search.searchBy')" value="{{ $searchKey }}">
                            <span class="input-group-btn"><button type="submit" class="btn waves-effect waves-light btn-info"><i class="fa fa-search"></i></button></span>
                        </div>
                    </form>
                    <h2 class="m-t-40">{{ __('modules.search.result', ['key' => $searchKey]) }}</h2>
                    <small>{{ __('modules.search.count', ['count' => $searchResults->total()]) }} </small>
                    <hr>
                    <ul class="search-listing">
                        @forelse($searchResults as $result)
                            <li>
                                <h3>
                                    <a href="{{ route($result->route_name, $result->searchable_id) }}">
                                        @lang('app.'.camel_case($result->searchable_type)): {{ $result->title }}
                                    </a>
                                </h3>
                                <a href="{{ route($result->route_name, $result->searchable_id) }}" class="search-links">{{ route($result->route_name, $result->searchable_id) }}</a>
                            </li>
                        @empty
                            <li>
                                @lang('modules.search.noResultFound')
                            </li>
                        @endforelse
                    </ul>
                    <div class="mt-4 d-flex justify-content-center" id="pagination">
                        {{ $searchResults->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer-script')
<script>
$('body').on('click', '#search-page #pagination a', function(e){
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var url = '{{ route("superadmin.search.show",$searchKey) }}?page='+page;
    $.get(url, function(response){
        $('#search-page').html(response.view);
    });
});
</script>
@endpush
