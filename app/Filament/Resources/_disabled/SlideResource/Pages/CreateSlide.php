<?php

namespace App\Filament\Resources\SlideResource\Pages;

use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\SlideResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSlide extends CreateRecord
{
    use ForceFillRecord;

    protected static string $resource = SlideResource::class;
}
