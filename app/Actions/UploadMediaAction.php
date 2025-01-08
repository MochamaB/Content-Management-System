<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class UploadMediaAction
{
    use AsAction;

    public function handle($model, $fieldName, $mediaCollection, $request)
    {
        // Handle the removal of existing photos
       
        if ($request->hasFile($fieldName)) {
            $model->clearMediaCollection($mediaCollection);
            $model->addMedia($request->file($fieldName))
                ->toMediaCollection($mediaCollection);
        }
    }

    public function editMediaUpload($model, $fieldName, $mediaCollection, $request)
    {
       
        if ($request->hasFile($fieldName)) {
            //   $model->clearMediaCollection($mediaCollection);
            $model->addMedia($request->file($fieldName))
                ->toMediaCollection($mediaCollection);
        }
    }

    
    public function UploadFile($uploadedFiles, $model)
    {
        foreach ($uploadedFiles as $file) {
            // Determine the collection based on MIME type
            $mimeType = $file->getMimeType();
            $collection = $this->getMediaCollectionForMimeType($mimeType);

            if ($collection) {
                // Add the file to the determined collection
                $model->addMedia($file)->toMediaCollection($collection);
            }
        }
    }
    /**
     * Get the media collection name based on the MIME type.
     */
    private function getMediaCollectionForMimeType($mimeType)
    {
        // Define mapping of MIME types to collections
        $mimeCollectionMap = [
            'images' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'],
            'videos' => ['video/mp4', 'video/quicktime', 'video/mpeg','video/mov'],
            'documents' => [
               'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ],
        ];

        foreach ($mimeCollectionMap as $collection => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes)) {
                return $collection;
            }
        }

        // Return null if no matching collection is found
        return null;
    }

    public function deleteFile($removedFilesIds, $model)
        {
            foreach ($removedFilesIds as $fileId) {
                $model->deleteMedia($fileId); // Delete media by ID
            }
        }
}
