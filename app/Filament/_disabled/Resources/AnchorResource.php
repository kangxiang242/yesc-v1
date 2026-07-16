<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnchorResource\Pages;
use App\Models\Anchor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnchorResource extends Resource
{
    protected static ?string $model = Anchor::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = '錨點管理';
    protected static ?string $label = '錨點';
    protected static ?string $pluralLabel = '錨點';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('名稱')
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('url')
                        ->label('網址')
                        ->columnSpan(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('名稱')->searchable(),
                Tables\Columns\TextColumn::make('url')->label('網址'),
                Tables\Columns\TextColumn::make('created_at')->label('創建時間')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnchors::route('/'),
            'create' => Pages\CreateAnchor::route('/create'),
            'edit' => Pages\EditAnchor::route('/{record}/edit'),
        ];
    }
}
