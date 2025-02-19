<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator as BasePathGenerator;

class CustomPathGenerator implements BasePathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $media->collection_name . '/';
    }

    /**
     * Get the path for conversion files.
     */
    public function getPathForConversions(Media $media): string
    {
        return $media->collection_name . '/conversions/';
    }

    /**
     * Get the path for responsive images.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $media->collection_name . '/responsive/';
    }
}
