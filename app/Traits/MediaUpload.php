<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


trait MediaUpload
{
    public function UploadNewImage($model, $fieldName, $mediaCollection, Request $request)
    {
        $request->validate([
            $fieldName => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);
        $model
            ->addMediaFromRequest($fieldName)
            ->toMediaCollection($mediaCollection);
    }

    public function EditImage($model, $fieldName, $mediaCollection, Request $request)
    {
        $request->validate([
            $fieldName => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);
    }

    public function UploadNewFile($model, $fieldName, $mediaCollection, Request $request)
    {
        $request->validate([
            $fieldName => 'required|file',
        ]);
        $model
            ->addMediaFromRequest($fieldName)
            ->toMediaCollection($mediaCollection);
    }

  

    public function deleteFile($path, $disk = 'public')
    {
        Storage::disk($disk)->delete($path);
    }
}