<?php

namespace App\Filament\Resources\AuthorResource\Pages;

use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\AuthorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuthor extends CreateRecord
{
    use ForceFillRecord;

    protected static string $resource = AuthorResource::class;
}
