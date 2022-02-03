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
        return Storage::disk('digitalocean')->putFileAs('images/'. $directory, $request->$input_name, $fileName ,'public');
    }

    public function multiUpload($request, $directory): array
    {
        $service_images_arr = [];
        $default_image_index = 0;

        foreach ($request->file as $fileData) {
            $extension = $fileData->getClientOriginalExtension();
            $fileName = time().Str::uuid().'.'.$extension;
            $imagePath = Storage::disk('digitalocean')->putFileAs('images/'. $directory, $fileData, $fileName ,'public');
            array_push($service_images_arr, $imagePath);
            if ($fileData->getClientOriginalName() == $imagePath) {
                $default_image_index = array_key_last($service_images_arr);
            }
        }

        return [
            "images" => $service_images_arr,
            "default_image" => count($service_images_arr) > 0 ? $service_images_arr[$default_image_index] : null
        ];
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
