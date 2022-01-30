<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FrontThemeSetting;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\FrontTheme\StoreImagesRequest;
use App\Http\Requests\FrontTheme\StoreTheme;
use App\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\FrontTheme\StoreSeoRequest;
use Illuminate\Support\Facades\File;

class FrontThemeSettingController extends SuperAdminBaseController
{

    public function store(StoreImagesRequest $request)
    {
        if (count($request->images) == 0) {
            return;
        }

        foreach ($request->images as $image) {
            $media = new Media();
            $media->image = Files::upload($image, 'carousel-images');
            $media->save();
        }

        $images = Media::select('id', 'image')->latest()->get();
        $view = view('partials.carousel_images', compact('images'))->render();

        return Reply::successWithData(__('messages.imageUploadedSuccessfully'), ['view' => $view]);
    }

    // @codingStandardsIgnoreLine
    public function update(StoreTheme $request, $id)
    {
        $theme = FrontThemeSetting::first();

        if($request->front_logo != null){

            $logo_path = base_path('public/user-uploads/front-logo/' . $theme->logo);

            if(File::exists($logo_path)) {
                File::delete($logo_path);
            }
        }

        if($request->favicon != null){

            $favicon_path = base_path('public/favicon/' . $theme->favicon);

            if(File::exists($favicon_path)) {
                File::delete($favicon_path);
            }
        }

        $theme->primary_color = $request->primary_color;
        $theme->secondary_color = $request->secondary_color;
        $theme->custom_css = $request->front_custom_css;
        $theme->title      = $request->front_title;

        if ($request->hasFile('front_logo')) {
            $theme->logo = Files::upload($request->front_logo, 'front-logo');
        }

        if ($request->hasFile('favicon')) {
            $theme->favicon = Files::upload($request->favicon, 'favicon');
        }

        $theme->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function destroy(Request $request, $id)
    {
        $req_image = Media::select('id', 'image')->where('id', $id)->first();

        if($req_image) {
            Files::deleteFile($req_image->file_name, 'carousel-images');
            $req_image->delete();
        }

        $images = Media::select('id', 'image')->latest()->get();

        $view = view('partials.carousel_images', compact('images'))->render();

        return Reply::successWithData(__('messages.imageDeletedSuccessfully'), ['view' => $view]);
    }

    public function addSeoDetails(StoreSeoRequest $request)
    {
        $seo = FrontThemeSetting::first();
        $seo->seo_description = $request->seo_description;
        $seo->seo_keywords = $request->seo_keywords;
        $seo->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
