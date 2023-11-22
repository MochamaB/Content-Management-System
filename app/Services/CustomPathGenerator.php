<?php

// app/Media/MediaPathGenerator.php

namespace App\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Illuminate\Support\Str;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $modelName = class_basename($media->model_type);
        return $modelName . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        $modelName = class_basename($media->model_type);
        return $modelName . '/conversions';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        $modelName = class_basename($media->model_type);
        return $modelName . '/responsive-images';
    }
}
