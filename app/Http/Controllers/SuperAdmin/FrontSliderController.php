<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Media;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CreateFrontSlider;
use App\Http\Requests\Front\UpdateFrontSlider;

class FrontSliderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()){

            $media = Media::all();

            return DataTables()->of($media)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';
                    $action .= '<a href="javascript:;" data-id="' . $row->id . '" class="btn btn-primary btn-circle edit-slider"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-slider"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('image', function ($row) {
                    return ' <img src="'.$row->image_url.'" border=""
                    width="150" height="75" class="img-thumbnail" align="center" alt=""/>';
                })
                ->editColumn('have_content', function ($row) {
                    return ucfirst($row->have_content);
                })
                ->addIndexColumn()
                ->rawColumns(['image', 'action'])
                ->toJson();
        }

        return view('superadmin.front-slider.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.front-slider.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateFrontSlider $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFrontSlider $request)
    {
        $image_name = time().'.png';

        if ($request->images) {
            $data = $request->images;

            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $path = public_path() . '/user-uploads/sliders/' . $image_name;

            file_put_contents($path, $data);
        }

        $media = new Media();

        if ($request->images) {
            $media->image = $image_name;
        }

        $media->have_content = $request->have_content;

        if ($request->have_content == 'yes') {
            $media->content = clean($request->slider_content);
            $media->subheading = $request->subheading;
            $media->heading = $request->heading;
            $media->open_tab = $request->tab;
            $media->content_alignment = $request->content_alignment;

            if($request->actionButton == 'custom'){
                $media->action_button = $request->custom_label;
                $media->url = $request->url;
            }

            if($request->actionButton == 'login'){
                $media->action_button = 'login';
                $media->url = route('login');
            }
        }
        else if($request->have_content == 'no') {
            $media->content = null;
            $media->action_button = null;
            $media->url = null;
            $media->open_tab = null;
            $media->content_alignment = null;
            $media->subheading = null;
            $media->heading = null;
        }

        $media->save();

        return Reply::success(__('messages.createdSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $frontSlide = Media::where('id', $id)->firstOrFail();
        return view('superadmin.front-slider.edit', compact('frontSlide'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateFrontSlider  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFrontSlider $request, $id)
    {
        $image_name = time().'.png';

        if ($request->images && $request->images !== 'data:,') {
            $data = $request->images;
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $path = public_path() . '/user-uploads/sliders/' . $image_name;

            file_put_contents($path, $data);
        }

        $media = Media::where('id', $id)->first();

        if ($request->images && $request->images !== 'data:,') {
            $media->image = $image_name;
        }

        $media->have_content = $request->have_content;

        if ($request->have_content == 'yes') {
            $media->content = clean($request->slider_content);
            $media->subheading = $request->subheading;
            $media->heading = $request->heading;
            $media->open_tab = $request->open_tab;
            $media->content_alignment = $request->content_alignment;

            if($request->actionButton == 'custom'){
                $media->action_button = $request->custom_label;
                $media->url = $request->url;
            }

            if($request->actionButton == 'login'){
                $media->action_button = 'login';
                $media->url = route('login');
            }
        }
        else if($request->have_content == 'no') {
            $media->content = null;
            $media->action_button = null;
            $media->url = null;
            $media->open_tab = null;
            $media->content_alignment = null;
            $media->subheading = null;
            $media->heading = null;
        }

        $media->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Media::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

}
