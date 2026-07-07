<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerDescResource\Pages;
use App\Models\BannerDesc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerDescResource extends Resource
{
    protected static ?string $model = BannerDesc::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationLabel = '橫幅描述';
    protected static ?string $label = '橫幅描述';
    protected static ?string $pluralLabel = '橫幅描述';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('標題')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('desc')
                        ->label('描述')
                        ->rows(4)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('標題')->searchable(),
                Tables\Columns\TextColumn::make('desc')->label('描述')->limit(60),
                Tables\Columns\TextColumn::make('created_at')->label('創建時間')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBannerDescs::route('/'),
            'create' => Pages\CreateBannerDesc::route('/create'),
            'edit' => Pages\EditBannerDesc::route('/{record}/edit'),
        ];
    }
}
