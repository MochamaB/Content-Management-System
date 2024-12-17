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
}
