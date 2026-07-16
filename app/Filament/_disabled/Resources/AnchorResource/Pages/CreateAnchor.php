<?php
namespace App\Filament\Resources\AnchorResource\Pages;
use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\AnchorResource;
use Filament\Resources\Pages\CreateRecord;
class CreateAnchor extends CreateRecord { use ForceFillRecord; protected static string $resource = AnchorResource::class; }
