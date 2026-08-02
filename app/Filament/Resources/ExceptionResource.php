<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExceptionResource\Pages;
use App\Models\Exception;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExceptionResource extends Resource
{
    protected static ?string $model = Exception::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = '異常日誌';
    protected static ?string $label = '異常';
    protected static ?string $pluralLabel = '異常';
    protected static ?string $navigationGroup = '系統管理';
    protected static ?int $navigationSort = 20;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('method')->label('方法'),
                Tables\Columns\TextColumn::make('uri')->label('路徑')->limit(40),
                Tables\Columns\TextColumn::make('referer')->label('引用頁')->limit(30),
                Tables\Columns\TextColumn::make('ip')->label('IP'),
                Tables\Columns\TextColumn::make('ip_country')->label('國家'),
                Tables\Columns\TextColumn::make('message')->label('錯誤信息')->limit(50),
                Tables\Columns\TextColumn::make('user_agent')->label('瀏覽器')->limit(50),
                Tables\Columns\TextColumn::make('created_at')->label('時間')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([Tables\Actions\ViewAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExceptions::route('/'),
            'view' => Pages\ViewException::route('/{record}'),
        ];
    }

    public static function canCreate(): bool { return false; }
}
