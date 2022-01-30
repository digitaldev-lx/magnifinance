<h3 class="box-title">@lang('modules.search.searchHere')</h3>
<form class="form-group" action="{{ route('superadmin.search.store') }}" novalidate method="POST" role="search">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="input-group">
        <input type="text" name="search_key" class="form-control" placeholder="@lang('modules.search.searchBy')"
            value="{{ $searchKey }}">
        <span class="input-group-btn"><button type="submit" class="btn waves-effect waves-light btn-info"><i
                    class="fa fa-search"></i></button></span>
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
            <a href="{{ route($result->route_name, $result->searchable_id) }}"
                class="search-links">{{ route($result->route_name, $result->searchable_id) }}</a>
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
