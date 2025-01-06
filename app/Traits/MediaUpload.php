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

    public function handleMediaOperations(Request $request, array $collections)
    {
        // Handle removed media
        if ($request->filled('removed_media')) {
            $removedMediaIds = explode(',', $request->input('removed_media'));
            
            foreach ($removedMediaIds as $mediaId) {
                if (!empty($mediaId)) {
                    $this->deleteMedia($mediaId);
                }
            }
        }

        // Handle new media uploads
        foreach ($collections as $collection) {
            $files = $request->file($collection, []);
            
            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file) {
                        $this->addMedia($file)
                             ->toMediaCollection($collection);
                    }
                }
            }
        }
    }

    /**
     * Get allowed file types for each collection
     *
     * @return array
     */
    public static function getAllowedFileTypes()
    {
        return [
            'images' => ['image/jpeg', 'image/png', 'image/gif'],
            'videos' => ['video/mp4', 'video/quicktime', 'video/mpeg'],
            'documents' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        ];
    }
}