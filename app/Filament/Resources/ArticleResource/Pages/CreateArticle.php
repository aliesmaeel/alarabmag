<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Filament\Support\ValidatesPublishedContent;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    use ValidatesPublishedContent;

    protected static string $resource = ArticleResource::class;
}
