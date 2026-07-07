<?php

namespace App\Filament\Pages;

use App\Models\AccessLog;
use App\Models\AnalyticsEvent;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class UserTrail extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static string $view = 'filament.pages.user-trail';
    protected static ?string $navigationLabel = '用戶軌跡';
    protected static ?string $title = '用戶軌跡';
    protected static ?string $navigationGroup = '系統管理';
    protected static ?int $navigationSort = 22;

    public function getHeading(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return '用戶軌跡';
    }

    public ?string $selectedIp = null;

    public function mount(): void
    {
        $this->selectedIp = request()->query('ip');
    }

    public function table(Table $table): Table
    {
        if ($this->selectedIp) {
            return $this->detailTable($table);
        }

        return $table
            ->query(
                AccessLog::selectRaw('ip as id, ip, COUNT(*) as visit_count, GROUP_CONCAT(DISTINCT device) as devices, MAX(created_at) as last_at, MIN(created_at) as first_at')
                    ->where('created_at', '>', now()->subDays(7))
                    ->groupBy('ip')
                    ->orderBy('last_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('ip')
                    ->label('用戶IP')
                    ->url(fn ($record) => '?ip=' . $record->ip),
                Tables\Columns\TextColumn::make('visit_count')->label('訪問次數')->sortable(),
                Tables\Columns\TextColumn::make('devices')->label('設備')
                    ->formatStateUsing(fn ($state) => implode(', ', array_unique(explode(',', $state)))),
                Tables\Columns\TextColumn::make('first_at')->label('首次訪問')->sortable(),
                Tables\Columns\TextColumn::make('last_at')->label('最近訪問')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('trail_filters')
                    ->form([
                        Forms\Components\TextInput::make('ip')->label('IP位址'),
                        Forms\Components\DatePicker::make('created_from')->label('開始時間'),
                        Forms\Components\DatePicker::make('created_until')->label('結束時間'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['ip'], fn ($q, $v) => $q->where('ip', $v))
                            ->when($data['created_from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['created_until'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    protected function detailTable(Table $table): Table
    {
        return $table
            ->query(AccessLog::where('ip', $this->selectedIp)->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('設備')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $ua = $record->user_agent;
                        if (! $ua) {
                            return '';
                        }
                        $device = \App\Filament\Support\DeviceInfo::device($ua);
                        $browser = \App\Filament\Support\DeviceInfo::browser($ua);

                        return '<p style="margin:0">' . e($device) . '</p>'
                             . '<p style="margin:0;font-size:0.75rem;color:#6b7280">' . e($browser ?? '未知') . '</p>';
                    })
                    ->tooltip(fn ($record) => $record->user_agent),
                Tables\Columns\TextColumn::make('created_at')->label('時間')->dateTime('Y-m-d H:i:s')->sortable(),
                Tables\Columns\TextColumn::make('url')->label('網址')->limit(60),
                Tables\Columns\TextColumn::make('method')->label('請求方式'),
                Tables\Columns\TextColumn::make('referer')->label('來源頁面')->limit(40),
                Tables\Columns\TextColumn::make('host')->label('域名'),
                Tables\Columns\TextColumn::make('device')->label('設備'),
                Tables\Columns\TextColumn::make('release_token')->label('版本')
                    ->size('sm')
                    ->copyable()
                    ->copyMessage('Token 已複製'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public function getTrailSummary(): string
    {
        if (!$this->selectedIp) return '';

        $ip = $this->selectedIp;
        $logs = AccessLog::where('ip', $ip)->orderBy('created_at')->limit(200)->get();

        if ($logs->isEmpty()) return '暫無記錄';

        $total = $logs->count();
        $uniqueIps = 1;
        $devices = $logs->pluck('device')->unique()->filter()->implode(', ');
        $firstAt = $logs->first()->created_at;
        $lastAt = $logs->last()->created_at;
        $diff = $firstAt->diffInMinutes($lastAt);
        $duration = $diff < 60 ? "{$diff}分鐘" : (int)($diff / 60) . "時" . ($diff % 60) . "分";

        return "IP: {$ip} | 訪問數: {$total} | 設備: {$devices} | 時間: {$firstAt->format('H:i')} ~ {$lastAt->format('H:i')}（{$duration}）";
    }

    protected function getViewData(): array
    {
        return [
            'selectedIp' => $this->selectedIp,
            'trailSummary' => $this->getTrailSummary(),
        ];
    }
}
