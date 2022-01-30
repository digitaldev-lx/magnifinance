<?php

namespace App\Http\Controllers\SuperAdmin;

use App\UniversalSearch;
use Illuminate\Http\Request;
use App\Helper\Reply;
;
use App\Http\Controllers\SuperAdminBaseController;

class SearchController extends SuperAdminBaseController
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

        return redirect(route('superadmin.search.show', $key));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $key
     * @return \Illuminate\Http\Response
     */
    public function show($key)
    {
        $this->searchResults = UniversalSearch::where('title', 'like', '%'.$key.'%')->where('company_id', null)->where('searchable_type', '!=', 'customer')->where('type', 'backend')->paginate(5);
        $this->searchKey = $key;
        session()->put('searchKey', $this->searchKey);

        if(request()->ajax()){

            $view = view('superadmin.search.ajax-show', $this->data)->render();
            return Reply::dataOnly(['view' => $view]);
        }

        return view('superadmin.search.show', $this->data);
    }

}
