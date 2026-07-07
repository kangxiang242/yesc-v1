<?php
namespace App\Filament\Resources\NavigationResource\Pages;
use App\Filament\Resources\NavigationResource;
use Filament\Resources\Pages\ListRecords;
class ListNavigations extends ListRecords { protected static string $resource = NavigationResource::class; protected function getHeaderActions(): array { return [\Filament\Actions\CreateAction::make()]; } }
