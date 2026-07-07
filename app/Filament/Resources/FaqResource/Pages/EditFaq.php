<?php
namespace App\Filament\Resources\FaqResource\Pages;
use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\FaqResource;
use Filament\Resources\Pages\EditRecord;
class EditFaq extends EditRecord { use ForceFillRecord; protected static string $resource = FaqResource::class; protected function getHeaderActions(): array { return [\Filament\Actions\DeleteAction::make()]; } }
