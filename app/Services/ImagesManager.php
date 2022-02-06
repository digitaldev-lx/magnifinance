<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImagesManager
{

    public function storeImage($request, $directory, $input_name = 'image'): string
    {
        $extension = $request->file($input_name)->getClientOriginalExtension();
        $fileName = time().Str::uuid().'.'.$extension;
        $filePath = 'images/'. $directory . '/' . $fileName;
        Storage::disk('digitalocean')->putFileAs('images/'. $directory, $request->$input_name, $fileName ,'public');
        return $filePath;
    }

    public function multiUpload($request, $directory): array
    {
        $service_images_arr = [];
        $default_image_index = 0;

        foreach ($request->file as $fileData) {
            $extension = $fileData->getClientOriginalExtension();
            $fileName = time().Str::uuid().'.'.$extension;
            $imagePath = 'images/'. $directory . '/'. $fileName;
            Storage::disk('digitalocean')->putFileAs('images/'. $directory, $fileData, $fileName ,'public');
            array_push($service_images_arr, $imagePath);
            if ($imagePath == $request->default_image) {
                $default_image_index = array_key_last($service_images_arr);
            }
        }

        return [$service_images_arr, $default_image_index];
    }

    public function storeImageBase64($base64string, $directory): string
    {
        $fileName = time().Str::uuid().'.png';
        $data = $base64string;
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $path = "images/$directory/" . $fileName;
        Storage::disk('digitalocean')->put($path, $data, 'public');
        return $path;
    }

    public function deleteImage($filePath): bool
    {
        return Storage::disk('digitalocean')->delete($filePath);
    }

    public function imageUrl($filePath): string
    {
        return Storage::disk('digitalocean')->url($filePath);
    }

    public function imageCdnUrl($filePath): string
    {
        return "https://". env("DIGITALOCEAN_SPACES_BUCKET") .".". env("DIGITALOCEAN_SPACES_REGION") .".cdn.digitaloceanspaces.com/". $filePath;
    }
}
