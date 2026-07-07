<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleTagResource\Pages;
use App\Models\ArticleCate;
use App\Models\ArticleTag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArticleTagResource extends Resource
{
    protected static ?string $model = ArticleTag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = '文章標籤';
    protected static ?string $label = '標籤';
    protected static ?string $pluralLabel = '標籤';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('標籤名稱')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->maxLength(255)
                        ->helperText('留空則由名稱自動生成'),
                    Forms\Components\ColorPicker::make('color')
                        ->label('標籤顏色'),
                    Forms\Components\Textarea::make('description')
                        ->label('描述')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('cat_ids')
                        ->label('關聯文章分類')
                        ->options(ArticleCate::pluck('name', 'id'))
                        ->multiple()
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('sort')
                        ->label('排序')
                        ->numeric()
                        ->default(0)
                        ->helperText('數值越大越靠前'),
                    Forms\Components\Toggle::make('status')
                        ->label('狀態')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('標籤名稱')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColorColumn::make('color')->label('顏色'),
                Tables\Columns\TextColumn::make('description')->label('描述')->limit(40),
                Tables\Columns\TextColumn::make('sort')->label('排序')->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('狀態')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')->label('創建時間')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('sort', 'desc')
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
            'index' => Pages\ListArticleTags::route('/'),
            'create' => Pages\CreateArticleTag::route('/create'),
            'edit' => Pages\EditArticleTag::route('/{record}/edit'),
        ];
    }
}
