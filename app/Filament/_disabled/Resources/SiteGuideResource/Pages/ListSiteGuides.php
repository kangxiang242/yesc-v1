<?php

namespace App\Filament\Resources\SiteGuideResource\Pages;

use App\Filament\Resources\SiteGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSiteGuides extends ListRecords
{
    protected static string $resource = SiteGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
