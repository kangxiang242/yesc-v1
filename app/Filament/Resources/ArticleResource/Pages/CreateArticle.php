<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    use ForceFillRecord;
    protected static string $resource = ArticleResource::class;
}
