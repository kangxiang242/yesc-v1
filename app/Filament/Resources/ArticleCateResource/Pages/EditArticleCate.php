<?php
namespace App\Filament\Resources\ArticleCateResource\Pages;
use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\ArticleCateResource;
use Filament\Resources\Pages\EditRecord;

class EditArticleCate extends EditRecord
{
    use ForceFillRecord;
    protected static string $resource = ArticleCateResource::class;
    protected function getHeaderActions(): array { return [\Filament\Actions\DeleteAction::make()]; }
}
