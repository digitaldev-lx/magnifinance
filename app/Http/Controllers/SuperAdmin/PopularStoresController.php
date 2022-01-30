<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Company;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PopularStoresController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()){

            $store = Company::where('popular_store', '1')->get();

            return DataTables()->of($store)
                ->addColumn('action', function ($row) {
                    return '<div class="text-right"> <a href="javascript:;" class="btn btn-danger btn-circle delete-store"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a></div>';
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->company_name);
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('superadmin.popular-stores.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Company::all();
        return view('superadmin.popular-stores.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company = Company::findOrFail($request->store_id);
        $company->popular_store = '1';
        $company->save();

        return Reply::success(__('messages.storeAddedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->popular_store = '0';
        $company->save();

        return Reply::success(__('messages.storeRemovedSuccessfully'));
    }

}
