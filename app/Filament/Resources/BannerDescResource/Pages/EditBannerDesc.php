<?php

namespace App\Filament\Resources\BannerDescResource\Pages;

use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\BannerDescResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBannerDesc extends EditRecord
{
    use ForceFillRecord;

    protected static string $resource = BannerDescResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
