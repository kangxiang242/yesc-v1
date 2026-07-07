<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationResource\Pages;
use App\Models\Navigation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NavigationResource extends Resource
{
    protected static ?string $model = Navigation::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = '導航管理';
    protected static ?string $label = '導航';
    protected static ?string $pluralLabel = '導航';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('parent_id')
                        ->label('上級導航')
                        ->options(Navigation::where('parent_id', 0)->pluck('name', 'id')->prepend('頂級', 0))
                        ->default(0)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('name')
                        ->label('名稱')
                        ->required()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('link')
                        ->label('鏈接')
                        ->columnSpan(1),
                    Forms\Components\FileUpload::make('ico')
                        ->label('圖標')
                        ->directory('navigation')
                        ->image()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('sort')
                        ->label('排序')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),
                    Forms\Components\Toggle::make('status')
                        ->label('狀態')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('parent_id')->label('上級')
                    ->formatStateUsing(fn ($state) => $state == 0 ? '頂級' : (Navigation::find($state)?->name ?? '-')),
                Tables\Columns\TextColumn::make('name')->label('名稱'),
                Tables\Columns\TextColumn::make('link')->label('鏈接'),
                Tables\Columns\ImageColumn::make('ico')->label('圖標')->circular(),
                Tables\Columns\TextColumn::make('sort')->label('排序')->sortable(),
                Tables\Columns\ToggleColumn::make('status')->label('狀態'),
            ])
            ->defaultSort('sort', 'asc')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavigations::route('/'),
            'create' => Pages\CreateNavigation::route('/create'),
            'edit' => Pages\EditNavigation::route('/{record}/edit'),
        ];
    }
}
