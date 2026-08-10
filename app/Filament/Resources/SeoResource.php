<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoResource\Pages;
use App\Models\Seo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeoResource extends Resource
{
    protected static ?string $model = Seo::class;
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'SEO管理';
    protected static ?string $label = 'SEO';
    protected static ?string $pluralLabel = 'SEO';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('path')
                        ->label('網址路徑')
                        ->columnSpan(1)
                        ->helperText('如：https://xxx.com/article 填入 /article 即可'),
                    Forms\Components\TextInput::make('title')
                        ->label('標題')
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('key_word')
                        ->label('關鍵字')
                        ->columnSpan(1),
                    Forms\Components\Textarea::make('description')
                        ->label('描述')
                        ->rows(3)
                        ->columnSpan(1),
                    Forms\Components\Radio::make('title_tail')
                        ->label('標題是否自動添加尾部')
                        ->options([0 => '否', 1 => '是'])
                        ->default(1)
                        ->columnSpan(1)
                        ->helperText('尾部内容在網站設置修改'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('path')->label('網址'),
                Tables\Columns\TextColumn::make('title')->label('標題')->limit(40),
                Tables\Columns\TextColumn::make('key_word')->label('關鍵字')->limit(30),
                Tables\Columns\TextColumn::make('description')->label('描述')->limit(40),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeos::route('/'),
            'create' => Pages\CreateSeo::route('/create'),
            'edit' => Pages\EditSeo::route('/{record}/edit'),
        ];
    }
}
