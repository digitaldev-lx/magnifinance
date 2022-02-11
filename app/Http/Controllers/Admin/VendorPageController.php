<?php

namespace App\Http\Controllers\Admin;

use App\Services\ImagesManager;
use App\Services\UrlManager;
use App\VendorPage;
use App\Helper\Files;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\VendorPage\UpdateVendorPageRequest;

class VendorPageController extends AdminBaseController
{

    private $image;

    public function __construct()
    {
        parent::__construct();
        $this->image = new ImagesManager();
        view()->share('pageTitle', __('menu.vendorPage'));
    }

    public function update(Request $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $vendorPage = VendorPage::findOrFail($id);
        $vendorPage->address = $request->address;
        $vendorPage->description = $request->description;
        $vendorPage->primary_contact = $request->primary_contact;
        $vendorPage->secondary_contact = $request->secondary_contact;
        $vendorPage->seo_description = $request->seo_description;
        $vendorPage->seo_keywords = $request->seo_keywords;
        $request->map_option ? $vendorPage->map_option = $request->map_option : $vendorPage->map_option = 'deactive';
        /*$vendorPage->latitude = $request->latitude ? $request->latitude : 0;
        $vendorPage->longitude = $request->longitude ? $request->longitude : 0;*/
        $vendorPage->lat_long = new Point($request->latitude, $request->longitude);	// (lat, lng),

        $vendorPage->og_image = $request->hasFile('og_image') ? $this->image->storeImage($request, 'vendor-page', 'og_image') : null;

        $vendorPage->save();

        return Reply::dataOnly(['defaultImage' => $request->default_image ?? 0]);
    }

    public function updateImages(Request $request)
    {
        $vendor_page = VendorPage::where('id', $request->vendor_page_id)->first();
        $vendor_page_images_arr = [];
        $default_image_index = 0;

        if ($request->hasFile('file')) {

            if ($request->file[0]->getClientOriginalName() !== 'blob') {

                $images = $this->image->multiUpload($request, 'vendor-page/' . $vendor_page->id);
                $vendor_page_images_arr = $images[0];
                $default_image_index = $images[1];

            }

            if ($request->uploaded_files) {

                $files = json_decode($request->uploaded_files, true);

                foreach ($files as $file) {
                    array_push($vendor_page_images_arr, $file['name']);

                    if ($file['name'] == $request->default_image) {
                        $default_image_index = array_key_last($vendor_page_images_arr);
                    }

                }

                $arr_diff = array_diff($vendor_page->photos, $vendor_page_images_arr);

                if (count($arr_diff) > 0) {
                    foreach ($arr_diff as $file) {
                        $this->image->deleteImage($file);
                    }
                }
            }
            else {
                if (!is_null($vendor_page->photos) && count($vendor_page->photos) > 0) {
                    $this->image->deleteImage($vendor_page->photos[0]);
                }
            }
        }

        $vendor_page->photos = json_encode(array_values($vendor_page_images_arr));
        $vendor_page->default_image = count($vendor_page_images_arr) > 0 ? $vendor_page_images_arr[$default_image_index] : null;
        $vendor_page->save();

        return Reply::redirect(route('admin.settings.index').'#vendor_page', __('messages.updatedSuccessfully'));
    }

}
