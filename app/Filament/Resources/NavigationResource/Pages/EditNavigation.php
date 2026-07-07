<?php
namespace App\Filament\Resources\NavigationResource\Pages;
use App\Filament\Resources\NavigationResource;
use Filament\Resources\Pages\EditRecord;
class EditNavigation extends EditRecord { protected static string $resource = NavigationResource::class; protected function getHeaderActions(): array { return [\Filament\Actions\DeleteAction::make()]; } }
