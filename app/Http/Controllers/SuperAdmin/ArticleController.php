<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Article;
use App\BusinessService;
use App\Category;
use App\EmployeeGroup;
use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Article\SaveArticle;
use App\Http\Requests\Article\StoreArticle;
use App\Http\Requests\Article\UpdateArticle;
use App\ItemTax;
use App\Location;
use App\Role;
use App\Scopes\CompanyScope;
use App\Services\ImagesManager;
use App\SmsSetting;
use App\Tax;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends SuperAdminBaseController
{

    private $image;
    public function __construct()
    {
        parent::__construct();
        $this->image = new ImagesManager();
        view()->share('pageTitle', __('menu.articles'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        abort_if(!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission('manage_article'), 403);

        if(\request()->ajax()){
            $articles = Article::withoutGlobalScope(CompanyScope::class)->get();

            return \datatables()->of($articles)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    $action .= '<a href="' . route('superadmin.articles.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    if (auth()->user()->is_superadmin && $row->status == 'pending') {
                        $action .= ' <a href="javascript:;" class="btn btn-success btn-circle approve-article"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('email.newLeave.approve').'"><i class="fa fa-check" aria-hidden="true"></i></a>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="'.asset($row->article_image_url).'" class="img" width="120em"/> ';
                })
                ->editColumn('status', function ($row) {
                    if($row->status == 'saved'){
                        return '<label class="badge badge-primary">'.__('app.saved').'</label>';
                    }
                    elseif($row->status == 'pending'){
                        return '<label class="badge badge-danger">'.__('app.pending').'</label>';
                    }else{
                        return '<label class="badge badge-success">'.__('app.approved').'</label>';

                    }
                })
                ->editColumn('company_id', function ($row) {
                    if(!is_null($row->company_id)){
                        return ucfirst($row->company->company_name);
                    }else{
                        return "SpotB";
                    }
                })
                ->editColumn('title', function ($row) {
                    return ucfirst($row->title);
                })
                ->editColumn('slug', function ($row) {
                    return ucfirst($row->slug);
                })
                ->editColumn('excerpt', function ($row) {
                    return ucfirst($row->excerpt);
                })
                ->editColumn('category_id', function ($row) {
                    return ucfirst($row->category->name);
                })
                ->editColumn('published_at', function ($row) {
                    return $row->published_at;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'status', 'slug', 'excerpt'])
                ->toJson();
        }

        return view('superadmin.articles.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_article'), 403);

        $categories = Cache::remember('categories', 60*60*24, function (){
            return Category::all();
        });

        return view('superadmin.articles.create', compact('categories'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function store(StoreArticle $request)
    {

        $article = new Article();

        $redirect_url = $request->redirect_url;

        $data = $request->except('redirect_url', 'data');
        $data['status'] = "saved";
        if ($request->hasFile('image')) {

            $filePath = $this->image->storeImage($request, 'article');

            $data['image'] = $filePath;
        }

        $article->create($data);

        return Reply::redirect($redirect_url, __('messages.createdSuccessfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  BusinessService  $businessService
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Article $article)
    {
        abort_if(!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission('manage_article'), 403);

        $categories = Category::withoutGlobalScope(CompanyScope::class)->orderBy('name', 'ASC')->get();

        return view('superadmin.articles.edit', compact(['categories', 'article']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateArticle $request
     * @param  Article $article
     * @return array
     */
    public function update(UpdateArticle $request, Article $article)
    {
        $article->title = $request->title;
        $article->slug = $request->slug;
        $article->category_id = $request->category_id;
        $article->excerpt = $request->excerpt;
        $article->content = $request->content;
        $article->keywords = $request->keywords;
        $article->seo_description = $request->seo_description;
        $article->status = $request->status;
        $article->published_at = $request->status == 'approved' ? Carbon::now()->format('Y-m-d H:i') : null;

        if ($request->hasFile('image')) {

            $this->image->deleteImage($article->image,'article');

            $filePath = $this->image->storeImage($request, 'article');

            $article->image = $filePath;
        }

        $article->save();

        return Reply::redirect(route('superadmin.articles.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateArticle $request
     * @param  Article $article
     * @return array
     */
    public function approve(Request $request, Article $article)
    {
        return $request->all();
        $article->status = "approved";
        $article->published_at = Carbon::now()->format('Y-m-d H:i');

        $article->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
