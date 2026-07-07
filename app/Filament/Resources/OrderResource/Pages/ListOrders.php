<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function refreshTable(): void
    {
        $this->dispatch('$refresh');
    }

    public function runExportOrders()
    {
        $table = $this->getTable();

        return $table->getAction('export_all')?->call();
    }
}
