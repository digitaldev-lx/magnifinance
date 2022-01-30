<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\UniversalSearch;
use Illuminate\Http\Request;
use App\Helper\Reply;
;
use App\Http\Controllers\AdminBaseController;

class SearchController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('front.search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $key = $request->search_key;

        if(trim($key) == ''){
            return redirect()->back();
        }

        return redirect(route('admin.search.show', $key));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $key
     * @return \Illuminate\Http\Response
     */
    public function show($key)
    {

        $this->searchKey = $key;
        session()->put('searchKey', $this->searchKey);

        $customers = User::withoutGlobalScopes()->with('customerBookings')->has('customerBookings')->where(function($query) use ($key) { $query->where('name', 'like', '%' . $key . '%')->orWhere('email', 'like', '%' . $key . '%');
        })->orderBy('id', 'desc')->pluck('id');

        $this->searchResults = UniversalSearch::withoutGlobalScopes()->where('title', 'like', '%'.$key.'%')->orWhere(function ($query) use ($customers){
            $query->where('company_id', null)->where('searchable_type', 'customer')->whereIn('searchable_id', $customers);
        })->where('type', 'backend')->paginate(5);

        if(request()->ajax()){

            $view = view('admin.search.ajax-show', $this->data)->render();
            return Reply::dataOnly(['view' => $view]);
        }

        return view('admin.search.show', $this->data);
    }

}
