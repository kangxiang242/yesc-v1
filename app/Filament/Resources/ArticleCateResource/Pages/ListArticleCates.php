<?php
namespace App\Filament\Resources\ArticleCateResource\Pages;
use App\Filament\Resources\ArticleCateResource;
use Filament\Resources\Pages\ListRecords;

class ListArticleCates extends ListRecords
{
    protected static string $resource = ArticleCateResource::class;
    protected function getHeaderActions(): array { return [\Filament\Actions\CreateAction::make()]; }
}
