<?php

namespace App\Filament\Resources;

use App\Exports\OrderXlsxExport;
use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\View\TablesRenderHook;
use Maatwebsite\Excel\Facades\Excel;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = '訂單管理';

    protected static ?string $label = '訂單';

    protected static ?string $pluralLabel = '訂單';
    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('訂單信息')
                    ->schema([
                        Forms\Components\TextInput::make('no')
                            ->label('訂單號')
                            ->disabled(),
                        Forms\Components\TextInput::make('inside_no')
                            ->label('內部訂單號')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_price')
                            ->label('訂單總價')
                            ->disabled(),
                        Forms\Components\Textarea::make('remarks')
                            ->label('客戶備注')
                            ->disabled()
                            ->rows(2),
                    ])->columns(3),

                Forms\Components\Section::make('商品明細')
                    ->schema([
                        Forms\Components\Repeater::make('products')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->label('商品名稱')
                                    ->disabled(),
                                Forms\Components\TextInput::make('number')
                                    ->label('數量')
                                    ->disabled(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('單價')
                                    ->disabled(),
                                Forms\Components\TextInput::make('total_price')
                                    ->label('商品小計')
                                    ->disabled(),
                            ])->columns(4)
                            ->disabled()
                            ->deletable(false)
                            ->addable(false)
                            ->reorderable(false),
                    ]),

                Forms\Components\Section::make('收貨人信息')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('姓名')
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->label('電話')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->label('郵箱')
                            ->disabled(),
                        Forms\Components\TextInput::make('delivery_type')
                            ->label('配送方式')
                            ->formatStateUsing(fn ($state) => \App\Models\Order::DELIVERY_TYPE_TXT[$state] ?? $state)
                            ->disabled(),
                        Forms\Components\TextInput::make('city')
                            ->label('城市')
                            ->disabled(),
                        Forms\Components\TextInput::make('county')
                            ->label('區')
                            ->disabled(),
                        Forms\Components\TextInput::make('street')
                            ->label('路')
                            ->disabled(),
                        Forms\Components\TextInput::make('address')
                            ->label('地址')
                            ->disabled(),
                    ])->columns(4),

                Forms\Components\Section::make('超商信息')
                    ->schema([
                        Forms\Components\TextInput::make('shop_no')
                            ->label('店鋪號')
                            ->disabled(),
                        Forms\Components\TextInput::make('shop_name')
                            ->label('店鋪名稱')
                            ->disabled(),
                    ])->columns(2)
                    ->visible(fn ($record) => $record && $record->delivery_type > 0),

                Forms\Components\Section::make('訂單處理')
                    ->schema([
                        Forms\Components\Radio::make('status')
                            ->label('訂單狀態')
                            ->options(\App\Models\Order::STATUS_TXT)
                            ->required(),
                        Forms\Components\Textarea::make('admin_remarks')
                            ->label('管理員備注')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('訂單號')
                    ->searchable()
                    ->sortable()
                    ->size('sm')
                    ,
                Tables\Columns\TextColumn::make('total_price')
                    ->label('金額')
                    ->sortable()
                    ->money('TWD')
                    ->size('sm')
                    ,
                Tables\Columns\TextColumn::make('products')
                    ->label('商品信息')
                    ->html()
                    ->wrap()
                    
                    ->getStateUsing(function ($record) {
                        $html = '';
                        foreach ($record->products as $item) {
                            $productName = e($item->product_name);
                            $html .= '<p style="width: 300px">' . $productName . '<span>(' . $item->number . '件)</span></p>';
                        }
                        return $html;
                    })
                    ->action(Tables\Actions\Action::make('view_products')
                        ->modalHeading('商品明細')
                        ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString(
                            view('filament.modals.order-products', ['order' => $record])->render()
                        ))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('關閉')
                        ->modalWidth('lg')),
                Tables\Columns\TextColumn::make('name')
                    ->label('收貨人信息')
                    ->searchable()
                    ->html()
                    ->wrap()
                    ->disabledClick()
                    ->formatStateUsing(function ($record) {
                        $nameCounts = \App\Models\Order::select('name', \Illuminate\Support\Facades\DB::raw('count(*) as cnt'))
                            ->groupBy('name')->pluck('cnt', 'name');
                        $count = $nameCounts[$record->name] ?? 0;
                        return '<p style="margin:0">' . '<a href="?name=' . e($record->name) . '">' . e($record->name) . '</a>'
                            . '<span>（' . $count . '）</span></p>'
                            . '<p style="margin:0"><a href="?phone=' . e($record->phone) . '">' . e($record->phone) . '</a></p>'
                            . '<p style="margin:0"><a href="?email=' . e($record->email) . '">' . e($record->email) . '</a></p>';
                    }),
                Tables\Columns\TextColumn::make('delivery_type')
                    ->label('配送方式')
                    
                    ->formatStateUsing(function ($record) {
                        $hasShopData = !empty($record->shop_name) || !empty($record->shop_no);

                        if ($hasShopData) {
                            if (!empty($record->shop_type)) {
                                return \App\Models\Order::SHOP_TYPE_TXT[$record->shop_type] ?? '7-11 超商';
                            }
                            return '7-11 超商';
                        }

                        if ($record->delivery_type !== null && $record->delivery_type !== '') {
                            return \App\Models\Order::DELIVERY_TYPE_TXT[$record->delivery_type] ?? '宅配到府';
                        }

                        return '宅配到府';
                    }),
                Tables\Columns\TextColumn::make('address')
                    ->label('地址')
                    ->html()
                    ->wrap()
                    
                    ->formatStateUsing(function ($record) {
                        if ($record->delivery_type > 0) {
                            $shopData = $record->shop_data ? (is_array($record->shop_data) ? $record->shop_data : json_decode($record->shop_data, true)) : null;
                            $shopAddr = $shopData['shop_address'] ?? $record->address ?? '';
                            $shopName = e($record->shop_name ?? '未知門市');
                            $shopNo = e($record->shop_no ?? '');
                            return '<p style="width: 150px">' . $shopName . '【' . $shopNo . '】<br/>' . e($shopAddr) . '</p>';
                        }
                        return '<p style="width: 150px">' . e($record->city . $record->county . $record->street . $record->address) . '</p>';
                    })
                    ->action(Tables\Actions\Action::make('view_address')
                        ->modalHeading('配送地址')
                        ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString(
                            view('filament.modals.order-address', ['order' => $record])->render()
                        ))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('關閉')
                        ->modalWidth('lg')),
                Tables\Columns\SelectColumn::make('status')
                    ->label('訂單狀態')
                    ->options(\App\Models\Order::STATUS_TXT)
                    ,

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('瀏覽器')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $ua = $record->user_agent;
                        if (!$ua) {
                            return '';
                        }
                        $device = \App\Filament\Support\DeviceInfo::device($ua);
                        $browser = \App\Filament\Support\DeviceInfo::browser($ua);
                        return '<p style="margin:0">' . e($device) . '</p>'
                             . '<p style="margin:0;font-size:0.75rem;color:#6b7280">' . e($browser ?? '未知') . '</p>';
                    })
                    ->tooltip(fn ($record) => $record->user_agent)
                    ,
                Tables\Columns\TextColumn::make('remarks')
                    ->label('備注')
                    ->limit(15)
                    ->action(null)
                    ->tooltip(fn ($record) => $record->remarks),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->searchable()
                    ->html()
                    ->wrap()
                    
                    ->formatStateUsing(function ($record) {
                        $ipCounts = \App\Models\Order::select('ip', \Illuminate\Support\Facades\DB::raw('count(*) as cnt'))
                            ->groupBy('ip')->pluck('cnt', 'ip');
                        $count = $ipCounts[$record->ip] ?? 0;
                        $html = '<p style="width: 130px;overflow: hidden;margin: 0">' . e($record->ip) . '</p>';
                        $html .= '<p style="margin: 0">' . e($record->ipcountry) . '</p>';
                        $html .= '<p>共' . $count . '單</p>';
                        return $html;
                    }),
                Tables\Columns\TextColumn::make('release_token')
                    ->label('版本')
                    ->size('sm')
                    ->copyable()
                    ->copyMessage('Token 已複製'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('下單時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->size('sm')
                    ->wrap(),
            ])
            ->searchable(false)
            ->defaultSort('created_at', 'desc')
            ->paginated([20, 50, 100])
            ->defaultPaginationPageOption(20)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filters([
                Tables\Filters\Filter::make('email')
                    ->label('郵箱')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('郵箱')
                            ->placeholder('輸入郵箱搜索'),
                    ])
                    ->query(fn (Builder $query, array $data) =>
                        $query->when($data['email'], fn (Builder $q, $value) => $q->where('email', 'like', "%{$value}%"))
                    ),
                Tables\Filters\SelectFilter::make('status')
                   ->label('訂單狀態')
                   ->options(\App\Models\Order::STATUS_TXT),
                Tables\Filters\SelectFilter::make('hide_test')
                    ->label('隱藏測試單')
                    ->options([
                        '' => '否',
                        '1' => '是',
                    ])
                    ->default('')
                    ->query(function (Builder $query, array $data) {
                        if (!($data['hide_test'] ?? false)) {
                            return;
                        }
                        $query->where(function (Builder $q) {
                            $q->where('is_test', 0)
                              ->where('name', 'not like', 'test%')
                              ->where('name', 'not like', '测试%')
                              ->where('name', 'not like', '測試%')
                              ->where('name', 'not like', '設備-%')
                              ->where('name', '!=', 'RainGor Ye');
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('quick_view')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->tooltip('查看完整資訊')
                    ->modalHeading('訂單資訊')
                    ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString(
                        view('filament.modals.order-detail', ['order' => $record])->render()
                        . '<div class="border-t pt-4 mt-4">'
                        . view('filament.modals.order-products', ['order' => $record])->render()
                        . '</div><div class="border-t pt-4 mt-4">'
                        . view('filament.modals.order-recipient', ['order' => $record])->render()
                        . '</div><div class="border-t pt-4 mt-4">'
                        . view('filament.modals.order-address', ['order' => $record])->render()
                        . '</div><div class="border-t pt-4 mt-4">'
                        . view('filament.modals.order-device', ['order' => $record])->render()
                        . '</div>'
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('關閉')
                    ->modalWidth('2xl'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('update_status')
                        ->label('批量修改狀態')
                        ->form([
                            Forms\Components\Radio::make('status')
                                ->label('訂單狀態')
                                ->options(\App\Models\Order::STATUS_TXT)
                                ->required(),
                        ])
                        ->action(function (array $data, \Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['status' => $data['status']]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('refresh')
                    ->label('刷新')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(function ($livewire) {
                        $livewire->dispatch('$refresh');
                    }),
                ActionGroup::make([
                    Action::make('export_all')
                        ->label('全部匯出')
                        ->icon('heroicon-o-document-text')
                        ->action(function () {
                            return static::exportOrdersToXlsx(Order::query(), 'orders-all-' . now()->format('Ymd-His') . '.xlsx');
                        }),
                    Action::make('export_selected')
                        ->label('匯出選中')
                        ->icon('heroicon-o-check-circle')
                        ->accessSelectedRecords()
                        ->action(function ($livewire) {
                            $selected = $livewire->getSelectedTableRecords();
                            return static::exportOrdersToXlsx($selected, 'orders-selected-' . now()->format('Ymd-His') . '.xlsx');
                        }),
                ])
                    ->label('匯出')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->button(),
            ]);

        static $registered = false;
        if (! $registered) {
            FilamentView::registerRenderHook(
                TablesRenderHook::TOOLBAR_END,
                fn () => view('filament.hooks.order-toolbar-buttons')->render(),
            );
            $registered = true;
        }
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * 匯出訂單為 XLSX（參考舊站 twshop 的 OrderExpoter 格式）。
     * 支援多選：$recordsOrQuery 為 Collection 時匯入選中 ID 查詢；
     * 傳入 Query 時直接用當前查詢（保持過濾條件）。
     */
    public static function exportOrdersToXlsx($recordsOrQuery, string $fileName)
    {
        // 構建查詢
        if ($recordsOrQuery instanceof \Illuminate\Database\Eloquent\Collection) {
            $ids = $recordsOrQuery->pluck('id');
            $query = Order::with('products')->whereIn('id', $ids);
        } else {
            $query = $recordsOrQuery->with('products');
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // 標題行（與舊站一致）
        $data[] = ['訂單號', '内單號', '商品', '總價', '名字', '電話', '郵箱', '地址', '收貨方式', '配送時間', '備注', '訂單狀態'];

        foreach ($orders as $item) {
            // 商品文字（格式：商品名(單價/件)*數量，多商品換行）
            $productTxt = '';
            foreach ($item->products as $k => $vv) {
                $productTxt .= $vv->product_name . "({$vv->unit_price}/件)*{$vv->number}";
                if (($k + 1) < count($item->products)) {
                    $productTxt .= PHP_EOL;
                }
            }

            // 地址：超商取貨格式為 {門市地址}（7-11{門市名}門市{門市號}）
            // 依生產資料，全部超商訂單皆為 7-11（shop_type 僅 0/1），故固定 7-11 前綴
            if ($item->delivery_type > 0) {
                $addr = $item->address . '（7-11' . $item->shop_name . '門市' . $item->shop_no . '）';
            } else {
                // 配送時間計算（與舊站一致）
                if ($item->delivery_time == 1) {
                    $gettime = '11:20:00';
                } elseif ($item->delivery_time == 2) {
                    $gettime = '14:35:00';
                } else {
                    $gettime = '18:50:00';
                }
                $parts = explode(':', $gettime);
                if ($parts[1] == '55') {
                    $parts[1] = '00';
                    $parts[0] = (int) $parts[0] + 1;
                    if ($parts[0] < 10) {
                        $parts[0] = '0' . $parts[0];
                    }
                } else {
                    $parts[1] = (int) $parts[1] + 5;
                    if ($parts[1] < 10) {
                        $parts[1] = '0' . $parts[1];
                    }
                }
                $updateGetTime = $parts[0] . ':' . $parts[1] . ':00';
                $addr = $item->city . $item->county . $item->street . $item->address
                    . '-請於' . substr($updateGetTime, 0, 5) . '前送達';
            }

            // 配送時段
            $deliveryTime = '09:00~12:00';
            if (!is_null($item->delivery_time)) {
                $deliveryTime = Order::DELIVERY_TIME[$item->delivery_time] ?? $deliveryTime;
            }

            $data[] = [
                $item->no,
                $item->inside_no,
                $productTxt,
                $item->total_price,
                $item->name,
                $item->phone,
                $item->email,
                $addr,
                '本人收貨',
                $deliveryTime,
                $item->remarks,
                Order::STATUS_TXT[$item->status] ?? $item->status,
            ];
        }

        return Excel::download(new OrderXlsxExport($data), $fileName);
    }
}
