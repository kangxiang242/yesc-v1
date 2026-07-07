<?php

namespace App\Filament\Resources\BannerDescResource\Pages;

use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\BannerDescResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBannerDesc extends CreateRecord
{
    use ForceFillRecord;

    protected static string $resource = BannerDescResource::class;
}
