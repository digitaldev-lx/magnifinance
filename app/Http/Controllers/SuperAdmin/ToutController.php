<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Tout;
use App\Article;
use App\Category;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Tout\StoreTout;
use App\Http\Requests\Tout\UpdateTout;
use App\Location;
use App\Scopes\CompanyScope;
use App\Services\ImagesManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ToutController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->image = new ImagesManager();
        view()->share('pageTitle', __('menu.toutes'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission(['read_tout']), 403);

        if (\request()->ajax()) {
            $toutes = Tout::orderByDesc('created_at')->get();

            return \datatables()->of($toutes)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    $action .= '<a href="' . route('superadmin.toutes.edit', $row->id) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    /*if (($this->user->is_admin || $this->user->can('delete_article')) && $row->id !== $this->user->id) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }*/

                    $action .= '</div>';

                    return $action;
                })
                /*->addColumn('image', function ($row) {
                    return '<img src="' . $row->tout_image_url . '" class="img" width="120em"/> ';
                })*/
                ->editColumn('status', function ($row) {
                    if ($row->status == 'pending') {
                        return '<label class="badge badge-primary">' . __('app.pending') . '</label>';
                    } else {
                        return '<label class="badge badge-success">' . __('app.completed') . '</label>';

                    }
                })
                ->editColumn('company', function ($row) {
                    return $row->company->company_name;
                })
                ->editColumn('period', function ($row) {
                    return $row->from . ' | '.$row->to;
                })
                ->editColumn('amount', function ($row) {
                    return $row->formated_amount_to_pay;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at;
                })
                ->editColumn('tout_local', function ($row) {
                    if ($row->category !== null) {
                        return '<label class="badge badge-primary">' . __('app.category') . '</label>';
                    } else {
                        return '<label class="badge badge-success">' . __('app.article') . '</label>';

                    }
                })
                ->editColumn('tout_in', function ($row) {
                    if (!is_null($row->article)) {
                        return '<label class="badge badge-primary">' . $row->article->limit_title . '</label>';
                    } elseif(!is_null($row->category)) {
                        return '<label class="badge badge-success">' . $row->category->name . '</label>';

                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'status', 'tout_local', 'tout_in'])
                ->toJson();
        }

        return view('superadmin.toutes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        /*abort_if(!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission(['create_tout']), 403);
        $id = $request->get('id');
        $article = !is_null($id) ? Article::withoutGlobalScopes()->whereId($id)->first() : null;
        $credentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        $locations = Location::withoutGlobalScope(CompanyScope::class)->orderBy('name', 'ASC')->get();
        $categories = Category::withoutGlobalScopes()->whereStatus('active')->get();
        $articles = Article::published()->withoutGlobalScope(CompanyScope::class)->get();
        $locale = App::getLocale();
        return view('superadmin.toutes.create', compact('categories', 'articles', 'article', 'locale', 'locations', 'credentials'));
    */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTout $request)
    {
        /*try{
            DB::beginTransaction();
            $tout = new Tout();
            $credentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

            $data = $request->except( 'data');
            $data['company_id'] = company()->id;
            $data['status'] = "pending";
            if ($request->hasFile('image')) {

                $filePath = $this->image->storeImage($request, 'toutes');

                $data['image'] = $filePath;
            }
            $tout = $tout->create($data);
            DB::commit();
            $tout = $tout->load('category', 'article');
            $locale = App::getLocale();
            $view = view('superadmin.toutes.tout_payment', compact('tout', 'locale', 'credentials'))->render();
        }catch (Exception $e){
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }

        return Reply::dataOnly(['status' => true, 'view' => $view]);*/
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /*view()->share('pageTitle', __('messages.paymentSuccess'));
        return view('admin.toutes.payment_success');*/
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tout = Tout::find($id);
        $locations = Location::withoutGlobalScope(CompanyScope::class)->orderBy('name', 'ASC')->get();
        $categories = Category::withoutGlobalScopes()->whereStatus('active')->get();
        $articles = Article::published()->withoutGlobalScope(CompanyScope::class)->get();
        $locale = App::getLocale();
        return view('superadmin.toutes.edit', compact('tout', 'locations', 'categories', 'articles', 'locale'));
        //todo: fazer o editar do anuncio e criar campos de contagem de impressões e cliques em cada anuncio
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTout $request, $id)
    {
        try {
            DB::beginTransaction();
            $tout = Tout::find($id);
            $tout->ads_in_all_category = $request->ads_in_all_category;
            $tout->category_id = $request->ads_in_all_category == 'yes' ? $request->category_id : null;
            $tout->article_id = $request->ads_in_all_category == 'no' ? $request->article_id : null;
            $tout->location_id = $request->location_id;
            $tout->description = $request->description;
            $tout->info1 = $request->info1;
            $tout->info2 = $request->info2;
            $tout->info3 = $request->info3;
            $tout->from = $request->from;
            $tout->to = $request->to;
            $tout->call_to_action = $request->call_to_action;
            $tout->link = $request->link;
            $tout->price = $request->price;
            $tout->status = $request->status;

            if ($request->hasFile('image')) {

                $this->image->deleteImage($tout->image,'toutes');

                $filePath = $this->image->storeImage($request, 'toutes');

                $tout->image = $filePath;
            }
            $tout->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }

        return Reply::redirect(route('superadmin.toutes.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
