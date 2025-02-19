<?php
namespace App\MediaLibrary;

use Spatie\MediaLibrary\Support\FileNamer\FileNamer;
use Spatie\MediaLibrary\Conversions\Conversion;
use Illuminate\Support\Str;

class CustomFileNamer extends FileNamer
{
    public function originalFileName(string $fileName): string
    {
        return (string) Str::uuid();
    }

    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $uniqueBaseName = $this->originalFileName($fileName);
        return "{$uniqueBaseName}-{$conversion->getName()}";
    }

    public function responsiveFileName(string $fileName): string
    {
        return $this->originalFileName($fileName);
    }
}
