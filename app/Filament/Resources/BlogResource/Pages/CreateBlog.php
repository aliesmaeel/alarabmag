<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use App\Filament\Support\ValidatesPublishedContent;
use Filament\Resources\Pages\CreateRecord;

class CreateBlog extends CreateRecord
{
    use ValidatesPublishedContent;

    protected static string $resource = BlogResource::class;
}
