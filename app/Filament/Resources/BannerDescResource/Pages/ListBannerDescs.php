<?php

namespace App\Filament\Resources\BannerDescResource\Pages;

use App\Filament\Resources\BannerDescResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBannerDescs extends ListRecords
{
    protected static string $resource = BannerDescResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
