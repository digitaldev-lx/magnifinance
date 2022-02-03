<?php

namespace App\Http\Controllers\SuperAdmin;

use App\BusinessService;
use App\Category;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Category\StoreCategory;
use Illuminate\Support\Facades\DB;
use App\Services\ImagesManager;

class CategoryController extends SuperAdminBaseController
{

    private $image;

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.categories'));
        $this->image = new ImagesManager();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_category', 'create_category', 'update_category', 'delete_category']));

        if (\request()->ajax()) {
            $categories = Category::all();

            return \datatables()->of($categories)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_category')) {
                        $action .= '<a href="' . route('superadmin.categories.edit', [$row->id]) . '" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_category')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . $row->category_image_url . '" class="img" height="65em" /> ';
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="badge badge-success">' . __('app.active') . '</label>';
                    } elseif ($row->status == 'deactive') {
                        return '<label class="badge badge-danger">' . __('app.deactive') . '</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'status'])
                ->toJson();
        }

        return view('superadmin.category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_category'));

        return view('superadmin.category.create');
    }

    /**
     * @param StoreCategory $request
     * @return array
     * @throws \Exception
     */
    public function store(StoreCategory $request)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_category'));
        try {
            DB::beginTransaction();
            $category = new Category();
            $data = $request->all();

            if ($request->hasFile('image')) {

                $filePath = $this->image->storeImage($request, 'category');

                $data['image'] = $filePath;
            }

            $category->create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }

        return Reply::redirect($request->redirect_url, __('messages.createdSuccessfully'));
    }

    /**
     * edit
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_category'));
        $this->category = $category;
        return view('superadmin.category.edit', $this->data);
    }

    /**
     * @param StoreCategory $request
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function update(StoreCategory $request, $id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_category'));
        $category = Category::find($id);
        $data = $request->all();
        if (!isset($request->only_blog)) {
            $data['only_blog'] = 'no';
        }
        try {
            DB::beginTransaction();
            if ($request->hasFile('image')) {

                $this->image->deleteImage($category->image, 'category');

                $filePath = $this->image->storeImage($request, 'category');
                $data['image'] = $filePath;
            }

            $category->update($data);

            // Update business services status for the category
            BusinessService::where('category_id', $id)->update(['status' => $request->status]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }

        return Reply::redirect(route('superadmin.categories.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        abort_403(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_category'));
        Category::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

}
