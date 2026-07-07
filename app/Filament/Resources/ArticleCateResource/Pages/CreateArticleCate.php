<?php
namespace App\Filament\Resources\ArticleCateResource\Pages;
use App\Filament\Concerns\ForceFillRecord;
use App\Filament\Resources\ArticleCateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticleCate extends CreateRecord
{
    use ForceFillRecord;
    protected static string $resource = ArticleCateResource::class;
}
