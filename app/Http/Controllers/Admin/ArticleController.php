<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\BusinessService;
use App\Category;
use App\EmployeeGroup;
use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ArticleController extends AdminBaseController
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
        abort_if(!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission('read_article'), 403);

        if(\request()->ajax()){
            $articles = Article::all();

            return \datatables()->of($articles)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    $action .= '<a href="' . route('admin.articles.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    /*if (($this->user->is_admin || $this->user->can('delete_article')) && $row->id !== $this->user->id) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }*/

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

        return view('admin.articles.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_article'), 403);

        $categories = Cache::remember('categories', 60*60*24, function (){
            return Category::all();
        });

        return view('admin.articles.create', compact('categories'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function store(StoreArticle $request)
    {
        try {
            DB::beginTransaction();
            $article = new Article();

            $redirect_url = $request->redirect_url;

            $data = $request->except('redirect_url', 'data');
            $data['company_id'] = company()->id;
            $data['status'] = "saved";
            if ($request->hasFile('image')) {

                $filePath = $this->image->storeImage($request, 'article');

                $data['image'] = $filePath;
            }

            $article->create($data);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }

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
        abort_if(!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission('update_article'), 403);

        $categories = Category::withoutGlobalScope(CompanyScope::class)->orderBy('name', 'ASC')->get();

        return view('admin.articles.edit', compact(['categories', 'article']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return array
     */
    public function update(UpdateArticle $request, Article $article)
    {
        try {
            DB::beginTransaction();
            $article->title = $request->title;
            $article->slug = $request->slug;
            $article->category_id = $request->category_id;
            $article->excerpt = $request->excerpt;
            $article->content = $request->content;
            $article->keywords = $request->keywords;
            $article->seo_description = $request->seo_description;
            $article->status = $request->status;

            if ($request->hasFile('image')) {

                $this->image->deleteImage($article->image,'article');

                $filePath = $this->image->storeImage($request, 'article');

                $article->image = $filePath;
            }

            $article->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }


        return Reply::redirect(route('admin.articles.index'), __('messages.updatedSuccessfully'));
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
