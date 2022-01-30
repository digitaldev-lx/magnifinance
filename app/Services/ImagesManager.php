<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImagesManager
{

    public function storeImage($request, $directory){
        $extension = $request->file('image')->getClientOriginalExtension();
        $fileName = time().Str::uuid().'.'.$extension;
        $filePath = $request->file('image')->storeAs('images/'. $directory, $fileName, 'public');
        return '/storage/' . $filePath;
    }

    public function deleteImage($filePath, $directory)
    {
        $filename = explode('/', $filePath);
        $filename = end($filename);
        $path = Storage::disk('public')->path('images/'.$directory.'/' . $filename);

        if(!file_exists($path)) {
            return false;
        }

        unlink($path);
        return true;
    }
}
