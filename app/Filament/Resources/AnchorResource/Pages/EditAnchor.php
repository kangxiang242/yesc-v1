<?php
namespace App\Filament\Resources\AnchorResource\Pages;
use App\Filament\Resources\AnchorResource;
use Filament\Resources\Pages\EditRecord;
class EditAnchor extends EditRecord { protected static string $resource = AnchorResource::class; protected function getHeaderActions(): array { return [\Filament\Actions\DeleteAction::make()]; } }
