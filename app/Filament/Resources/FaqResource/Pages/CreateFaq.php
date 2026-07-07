<?php
namespace App\Filament\Resources\FaqResource\Pages;
use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\FaqResource;
use Filament\Resources\Pages\CreateRecord;
class CreateFaq extends CreateRecord { use ForceFillRecord; protected static string $resource = FaqResource::class; }
