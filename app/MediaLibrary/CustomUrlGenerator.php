<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class CustomUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        return '/storage/uploads/' . ltrim($this->getPathRelativeToRoot(), '/');
    }
}

