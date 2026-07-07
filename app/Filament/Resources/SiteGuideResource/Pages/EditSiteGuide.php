<?php

namespace App\Filament\Resources\SiteGuideResource\Pages;

use App\Filament\Resources\SiteGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiteGuide extends EditRecord
{
    protected static string $resource = SiteGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
