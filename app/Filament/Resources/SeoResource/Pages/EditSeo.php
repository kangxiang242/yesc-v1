<?php
namespace App\Filament\Resources\SeoResource\Pages;
use App\Filament\Resources\SeoResource;
use Filament\Resources\Pages\EditRecord;
class EditSeo extends EditRecord { protected static string $resource = SeoResource::class; protected function getHeaderActions(): array { return [\Filament\Actions\DeleteAction::make()]; } }
