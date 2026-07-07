<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccessLogResource\Pages;
use App\Models\AccessLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccessLogResource extends Resource
{
    protected static ?string $model = AccessLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = '訪問日誌';
    protected static ?string $label = '訪問日誌';
    protected static ?string $pluralLabel = '訪問日誌';
    protected static ?string $navigationGroup = '系統管理';
    protected static ?int $navigationSort = 21;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('時間')->dateTime('Y-m-d H:i:s')->sortable()->size('sm'),
                Tables\Columns\TextColumn::make('ip')->label('IP')->searchable()
                    ->description(fn ($record) => $record->device),
                Tables\Columns\TextColumn::make('url')->label('網址')->limit(50)
                    ->tooltip(fn ($record) => $record->url),
                Tables\Columns\TextColumn::make('method')->label('方式')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'GET' => 'info',
                        'POST' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('referer')->label('來源')->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('瀏覽器')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $ua = $record->user_agent;
                        if (!$ua) return '';
                        $device = \App\Filament\Support\DeviceInfo::device($ua);
                        $browser = \App\Filament\Support\DeviceInfo::browser($ua);
                        return '<p style="margin:0">' . e($device) . '</p>'
                             . '<p style="margin:0;font-size:0.75rem;color:#6b7280">' . e($browser ?? '未知') . '</p>';
                    })
                    ->tooltip(fn ($record) => $record->user_agent),
                Tables\Columns\TextColumn::make('host')->label('域名')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('release_token')->label('版本')
                    ->size('sm')
                    ->copyable()
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([50, 100, 200])
            ->defaultPaginationPageOption(50)
            ->filters([
                Tables\Filters\Filter::make('filters')
                    ->form([
                        Forms\Components\TextInput::make('ip')->label('IP'),
                        Forms\Components\TextInput::make('url')->label('網址'),
                        Forms\Components\Select::make('method')->label('請求方式')->options(['GET' => 'GET', 'POST' => 'POST']),
                        Forms\Components\TextInput::make('host')->label('域名'),
                        Forms\Components\Select::make('device')->label('設備')->multiple()
                            ->options(['iphone' => 'iPhone', 'android' => 'Android', 'ipad' => 'iPad', 'windows' => 'Windows', 'mac' => 'Mac', 'linux' => 'Linux']),
                        Forms\Components\DatePicker::make('created_from')->label('開始日期'),
                        Forms\Components\DatePicker::make('created_until')->label('結束日期'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['ip'], fn ($q, $v) => $q->where('ip', $v))
                            ->when($data['url'], fn ($q, $v) => $q->where('url', $v))
                            ->when($data['method'], fn ($q, $v) => $q->where('method', $v))
                            ->when($data['host'], fn ($q, $v) => $q->where('host', $v))
                            ->when($data['device'], fn ($q, $v) => $q->whereIn('device', $v))
                            ->when($data['created_from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['created_until'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccessLogs::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
}
