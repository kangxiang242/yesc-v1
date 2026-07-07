<?php
namespace App\Filament\Resources\BannerResource\Pages;
use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\BannerResource;
use Filament\Resources\Pages\EditRecord;

class EditBanner extends EditRecord
{
    use ForceFillRecord;
    protected static string $resource = BannerResource::class;
    protected function getHeaderActions(): array { return [\Filament\Actions\DeleteAction::make()]; }
}
