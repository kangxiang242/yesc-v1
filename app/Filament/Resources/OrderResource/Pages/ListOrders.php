<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Exports\OrdersExport;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function refreshTable(): void
    {
        $this->dispatch('$refresh');
    }

    /**
     * 防止連續點擊匯出：原子鎖，8 秒內重複觸發視為無效。
     */
    protected function acquireExportLock(): bool
    {
        $key = 'order_export_lock_' . (auth()->id() ?? 'guest');

        return Cache::add($key, true, 8);
    }

    public function exportAll()
    {
        if (! $this->acquireExportLock()) {
            Notification::make()
                ->warning()
                ->title('匯出進行中')
                ->body('請稍候，避免連續點擊')
                ->send();

            return;
        }

        set_time_limit(0);

        $total = Order::count();
        Notification::make()
            ->info()
            ->title("正在匯出 {$total} 筆訂單，請稍候...")
            ->body('處理完成後將自動下載')
            ->send();

        $data = OrderResource::buildExportData(
            Order::with('products')->orderBy('created_at', 'desc')->lazy()
        );

        return Excel::download(new OrdersExport($data), '訂單匯出-' . date('YmdHis') . '.xlsx');
    }

    public function exportSelectedWithIds(array $ids = [])
    {
        if (empty($ids)) {
            Notification::make()
                ->warning()
                ->title('未選中任何訂單')
                ->body('請先勾選要匯出的訂單')
                ->send();
            return;
        }

        set_time_limit(0);
        $records = Order::with('products')->whereIn('id', $ids)->get();

        if ($records->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('未找到對應訂單')
                ->body('請確認訂單是否仍然存在')
                ->send();
            return;
        }

        Notification::make()
            ->info()
            ->title("正在匯出 {$records->count()} 筆訂單...")
            ->body('處理完成後將自動下載')
            ->send();

        $data = OrderResource::buildExportData($records);

        return Excel::download(new OrdersExport($data), '訂單匯出-' . date('YmdHis') . '.xlsx');
    }

    public function exportSelected()
    {
        if (! $this->acquireExportLock()) {
            Notification::make()
                ->warning()
                ->title('匯出進行中')
                ->body('請稍候，避免連續點擊')
                ->send();

            return;
        }

        $records = $this->getSelectedTableRecords();
        if ($records->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('未選中任何訂單')
                ->body('請先勾選要匯出的訂單')
                ->send();
            return;
        }

        set_time_limit(0);
        $records->loadMissing('products');
        $data = OrderResource::buildExportData($records);

        Notification::make()
            ->info()
            ->title("正在匯出 {$records->count()} 筆訂單...")
            ->body('處理完成後將自動下載')
            ->send();

        return Excel::download(new OrdersExport($data), '訂單匯出-' . date('YmdHis') . '.xlsx');
    }
}