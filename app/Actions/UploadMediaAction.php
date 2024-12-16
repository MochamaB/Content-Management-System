<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class UploadMediaAction
{
    use AsAction;

    public function handle($model, $fieldName, $mediaCollection, $request)
    {
        if ($request->hasFile($fieldName)) {
            $model->clearMediaCollection($mediaCollection);
            $model->addMedia($request->file($fieldName))
                ->toMediaCollection($mediaCollection);
        }
    }

    public function multipleUpload($model, $fieldName, $mediaCollection, $request)
    {
        if ($request->hasFile($fieldName)) {
            // Optional: Clear the existing media collection if you want fresh uploads
            $model->clearMediaCollection($mediaCollection);

            // Iterate through all uploaded files and add them to the collection
            foreach ($request->file($fieldName) as $file) {
                $model->addMedia($file)
                    ->toMediaCollection($mediaCollection);
            }
        }
    }
}
