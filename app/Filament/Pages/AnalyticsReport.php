<?php

namespace App\Filament\Pages;

use App\Models\AnalyticsEvent;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnalyticsReport extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static string $view = 'filament.pages.analytics-report';
    protected static ?string $navigationLabel = '行為分析';
    protected static ?string $title = '行為分析';

    public function getHeading(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return '行為分析';
    }
    protected static ?string $navigationGroup = '系統管理';
    protected static ?int $navigationSort = 23;

    public function table(Table $table): Table
    {
        return $table
            ->query(AnalyticsEvent::orderBy('id', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('時間')->dateTime('Y-m-d H:i:s')->sortable(),
                Tables\Columns\TextColumn::make('platform')->label('平台')
                    ->formatStateUsing(fn ($state) => $state === 'web' ? 'WEB' : 'M'),
                Tables\Columns\TextColumn::make('event_name')->label('事件')->badge(),
                Tables\Columns\TextColumn::make('page_type')->label('頁面'),
                Tables\Columns\TextColumn::make('page_path')->label('路徑')->limit(40),
                Tables\Columns\TextColumn::make('visitor_id')->label('訪客')
                    ->formatStateUsing(fn ($state) => mb_substr($state ?? '', 0, 8) . '...'),
                Tables\Columns\TextColumn::make('ip')->label('IP'),
                Tables\Columns\TextColumn::make('element_id')->label('元素')->limit(20),
                Tables\Columns\TextColumn::make('props')->label('屬性')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_UNESCAPED_UNICODE) : '')
                    ->limit(60),
            ])
            ->filters([
                Tables\Filters\Filter::make('analytics_filters')
                    ->form([
                        Forms\Components\Select::make('platform')->label('平台')
                            ->options(['web' => 'WEB', 'mobile' => 'M']),
                        Forms\Components\Select::make('event_name')->label('事件')
                            ->options([
                                'page_view' => 'page_view', 'page_leave' => 'page_leave',
                                'click' => 'click', 'scroll_milestone' => 'scroll_milestone',
                                'block_view' => 'block_view', 'field_interact' => 'field_interact',
                                'validation_error' => 'validation_error', 'area_load' => 'area_load',
                                'order_submit' => 'order_submit', 'order_submit_error' => 'order_submit_error',
                                'message_submit' => 'message_submit', 'message_submit_error' => 'message_submit_error',
                            ]),
                        Forms\Components\TextInput::make('ip')->label('IP'),
                        Forms\Components\DatePicker::make('created_from')->label('開始日期'),
                        Forms\Components\DatePicker::make('created_until')->label('結束日期'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['platform'], fn ($q, $v) => $q->where('platform', $v))
                            ->when($data['event_name'], fn ($q, $v) => $q->where('event_name', $v))
                            ->when($data['ip'], fn ($q, $v) => $q->where('ip', $v))
                            ->when($data['created_from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['created_until'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public function getSummaryStats(): array
    {
        return [
            'web_pv' => AnalyticsEvent::where('platform', 'web')->where('event_name', 'page_view')->count(),
            'mobile_pv' => AnalyticsEvent::where('platform', 'mobile')->where('event_name', 'page_view')->count(),
            'order_submits' => AnalyticsEvent::where('event_name', 'order_submit')->count(),
            'page_leaves' => AnalyticsEvent::where('event_name', 'page_leave')->count(),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getSummaryStats(),
        ];
    }
}
