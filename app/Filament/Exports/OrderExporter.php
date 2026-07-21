<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('no')
                ->label('訂單號'),
            ExportColumn::make('inside_no')
                ->label('內部單號'),
            ExportColumn::make('name')
                ->label('收件人'),
            ExportColumn::make('phone')
                ->label('聯絡電話'),
            ExportColumn::make('email')
                ->label('電子郵件'),
            ExportColumn::make('total_price')
                ->label('總金額'),
            ExportColumn::make('product_price')
                ->label('商品金額'),
            ExportColumn::make('freight')
                ->label('運費'),
            ExportColumn::make('delivery_type')
                ->label('配送方式')
                ->formatStateUsing(fn ($state) => Order::DELIVERY_TYPE_TXT[(string) $state] ?? $state),
            ExportColumn::make('delivery_time')
                ->label('配送時段')
                ->formatStateUsing(fn ($state) => Order::DELIVERY_TIME[(string) $state] ?? $state),
            ExportColumn::make('status')
                ->label('訂單狀態')
                ->formatStateUsing(fn ($state) => Order::STATUS_TXT[(string) $state] ?? $state),
            ExportColumn::make('products')
                ->label('商品明細')
                ->formatStateUsing(fn ($record) => $record->products->map(fn ($p) =>
                    "{$p->product_name}({$p->unit_price}/件)*{$p->number}"
                )->implode(PHP_EOL)),
            ExportColumn::make('address')
                ->label('收貨地址')
                ->formatStateUsing(function ($record) {
                    if ($record->delivery_type > 0) {
                        // 超商取貨：{門市地址}（7-11{門市名}門市{門市號}）
                        // 依生產資料，全部超商訂單皆為 7-11（shop_type 僅 0/1），故固定 7-11 前綴
                        return $record->address . '（7-11' . $record->shop_name . '門市' . $record->shop_no . '）';
                    }

                    // 宅配到府
                    return trim(implode(' ', array_filter([
                        $record->city ?? '',
                        $record->county ?? '',
                        $record->street ?? '',
                        $record->address ?? '',
                    ])));
                }),
            ExportColumn::make('shop_name')
                ->label('門市名稱'),
            ExportColumn::make('shop_no')
                ->label('門市編號'),
            ExportColumn::make('shop_type')
                ->label('門市類型')
                ->formatStateUsing(fn ($state) => $state ? (Order::SHOP_TYPE_TXT[(string) $state] ?? $state) : ''),
            ExportColumn::make('created_at')
                ->label('下單時間'),
        ];
    }

    public static function getCompletedNotificationBody(\Filament\Actions\Exports\Models\Export $export): string
    {
        $body = '訂單匯出完成，共處理 ' . number_format($export->successful_rows) . ' 筆。';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' 筆失敗。';
        }

        return $body;
    }
}
