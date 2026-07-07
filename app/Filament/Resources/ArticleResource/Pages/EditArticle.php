<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\ArticleResource;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    use ForceFillRecord;
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\DeleteAction::make()];
    }
}
