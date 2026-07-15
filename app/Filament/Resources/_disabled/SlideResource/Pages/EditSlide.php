<?php

namespace App\Filament\Resources\SlideResource\Pages;

use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\SlideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSlide extends EditRecord
{
    use ForceFillRecord;

    protected static string $resource = SlideResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
