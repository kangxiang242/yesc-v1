<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = '留言管理';
    protected static ?string $label = '留言';
    protected static ?string $pluralLabel = '留言';
    protected static ?string $navigationGroup = '系統管理';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('留言信息')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('姓名')->disabled(),
                    Forms\Components\TextInput::make('phone')->label('電話')->disabled(),
                    Forms\Components\TextInput::make('email')->label('郵箱')->disabled(),
                    Forms\Components\Textarea::make('content')->label('內容')->disabled()->rows(4)->columnSpanFull(),
                    Forms\Components\TextInput::make('ip')->label('IP')->disabled(),
                    Forms\Components\TextInput::make('user_agent')->label('瀏覽器')->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('關聯訂單')
                ->schema([
                    Forms\Components\Placeholder::make('related_orders')
                        ->label('')
                        ->content(function ($record) {
                            if (!$record || (empty($record->phone) && empty($record->email))) {
                                return '無聯絡方式，無法關聯訂單';
                            }

                            $orders = self::relatedOrders($record);
                            if ($orders->isEmpty()) {
                                $contact = $record->phone ?? $record->email;
                                return '該聯絡方式（' . e($contact) . '）無對應訂單';
                            }

                            $html = '<table style="width:100%;border-collapse:collapse;font-size:13px">';
                            $html .= '<thead><tr style="background:#f3f4f6">';
                            $html .= '<th style="padding:8px;text-align:left;border-bottom:2px solid #e5e7eb">訂單號</th>';
                            $html .= '<th style="padding:8px;text-align:left;border-bottom:2px solid #e5e7eb">總價</th>';
                            $html .= '<th style="padding:8px;text-align:left;border-bottom:2px solid #e5e7eb">配送方式</th>';
                            $html .= '<th style="padding:8px;text-align:left;border-bottom:2px solid #e5e7eb">狀態</th>';
                            $html .= '<th style="padding:8px;text-align:left;border-bottom:2px solid #e5e7eb">時間</th>';
                            $html .= '</tr></thead><tbody>';

                            foreach ($orders as $order) {
                                $status = Order::STATUS_TXT[$order->status] ?? $order->status;

                                $hasShopData = !empty($order->shop_name) || !empty($order->shop_no);
                                if ($hasShopData) {
                                    if (!empty($order->shop_type)) {
                                        $delivery = Order::SHOP_TYPE_TXT[$order->shop_type] ?? '7-11 超商';
                                    } else {
                                        $delivery = '7-11 超商';
                                    }
                                } elseif ($order->delivery_type !== null && $order->delivery_type !== '') {
                                    $delivery = Order::DELIVERY_TYPE_TXT[$order->delivery_type] ?? '宅配到府';
                                } else {
                                    $delivery = '宅配到府';
                                }

                                $html .= '<tr>';
                                $html .= '<td style="padding:8px;border-bottom:1px solid #e5e7eb"><a href="/' . config('global.admin_path') . '/orders/' . $order->id . '/edit" target="_blank" style="color:#2563eb">' . e($order->no) . '</a></td>';
                                $html .= '<td style="padding:8px;border-bottom:1px solid #e5e7eb">NT$' . number_format($order->total_price) . '</td>';
                                $html .= '<td style="padding:8px;border-bottom:1px solid #e5e7eb">' . e($delivery) . '</td>';
                                $html .= '<td style="padding:8px;border-bottom:1px solid #e5e7eb">' . e($status) . '</td>';
                                $html .= '<td style="padding:8px;border-bottom:1px solid #e5e7eb">' . $order->created_at->format('Y-m-d H:i') . '</td>';
                                $html .= '</tr>';
                            }

                            $html .= '</tbody></table>';
                            return new HtmlString($html);
                        }),
                ])->visible(fn ($record) => $record && (!empty($record->phone) || !empty($record->email))),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable()->size('sm'),
                Tables\Columns\TextColumn::make('name')->label('姓名')
                    ->description(fn ($record) => $record->phone),
                Tables\Columns\TextColumn::make('email')->label('郵箱')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('content')->label('內容')->limit(50)
                    ->tooltip(fn ($record) => $record->content),
                Tables\Columns\TextColumn::make('related_orders')
                    ->label('關聯訂單')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if (empty($record->phone) && empty($record->email)) return '';

                        $relatedOrders = self::relatedOrders($record);
                        if ($relatedOrders->isEmpty()) return '';

                        $html = '';
                        foreach ($relatedOrders as $i => $order) {
                            if ($i > 0) $html .= '<br>';
                            $status = Order::STATUS_TXT[$order->status] ?? $order->status;
                            $html .= e($order->no) . ' <span style="color:#6b7280;font-size:0.85em">[' . e($status) . ']</span>';
                        }
                        return $html;
                    })
                    ->tooltip(function ($record) {
                        if (empty($record->phone) && empty($record->email)) return null;
                        $orders = self::relatedOrders($record);
                        if ($orders->isEmpty()) return null;
                        $lines = [];
                        foreach ($orders as $order) {
                            $status = Order::STATUS_TXT[$order->status] ?? $order->status;
                            $lines[] = $order->no . ' (' . $status . ') NT$' . number_format($order->total_price);
                        }
                        return implode("\n", $lines);
                    }),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP / 設備')
                    ->searchable()
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $html = e($record->ip);
                        if ($record->user_agent) {
                            $device = \App\Filament\Support\DeviceInfo::device($record->user_agent);
                            $browser = \App\Filament\Support\DeviceInfo::browser($record->user_agent);
                            $html .= '<br><small>' . e($device);
                            if ($browser) {
                                $html .= ' / ' . e($browser);
                            }
                            $html .= '</small>';
                        }
                        return $html;
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('時間')->dateTime('Y-m-d H:i')->sortable()->size('sm'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([20, 50, 100])
            ->recordAction('view')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modal()
                    ->modalHeading('留言詳情')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('關閉'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }

    /**
     * yescialis 的 Message 模型未定义 related_orders 关联，
     * 这里按手机/邮箱手动关联订单。
     */
    private static function relatedOrders($record)
    {
        if (! $record || (empty($record->phone) && empty($record->email))) {
            return collect();
        }

        return \App\Models\Order::query()
            ->where(function ($q) use ($record) {
                $q->when($record->phone, fn ($q2, $v) => $q2->where('phone', $v))
                  ->when($record->email, fn ($q2, $v) => $q2->orWhere('email', $v));
            })
            ->orderByDesc('created_at')
            ->get();
    }
}
