<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteGuideResource\Pages;
use App\Filament\Resources\SiteGuideResource\RelationManagers;
use App\Models\SiteGuide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiteGuideResource extends Resource
{
    protected static ?string $model = SiteGuide::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = '頁面配置';
    protected static ?string $label = '頁面配置';
    protected static ?string $pluralLabel = '頁面配置';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('page_type')
                    ->label('頁面類型')
                    ->options([
                        'home' => '首頁',
                        'product' => '產品頁',
                    ])
                    ->default('home')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('標題')
                    ->maxLength(255)
                    ->placeholder('輸入頁面標題'),
                Forms\Components\Textarea::make('description')
                    ->label('介紹描述')
                    ->rows(4)
                    ->columnSpanFull()
                    ->placeholder('輸入頁面介紹描述'),
                Forms\Components\Section::make('選項配置')
                    ->schema([
                        Forms\Components\TextInput::make('item_title')
                            ->label('選項標題')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('item_description')
                            ->label('選項描述')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('item_image')
                            ->label('選項圖片')
                            ->image()
                            ->directory('site-guides')
                            ->maxSize(2048),
                    ])
                    ->collapsed()
                    ->description('可選配置項'),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('page_type')
                    ->label('頁面類型')
                    ->badge()
                    ->color(fn ($state) => $state === 'home' ? 'primary' : 'warning')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'home' => '首頁',
                        'product' => '產品頁',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label('標題')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('item_title')
                    ->label('選項標題')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\ImageColumn::make('item_image')
                    ->label('選項圖片')
                    ->square()
                    ->size(50),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('狀態')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('創建時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('sort', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('page_type')
                    ->label('頁面類型')
                    ->options([
                        'home' => '首頁',
                        'product' => '產品頁',
                    ]),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('狀態'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteGuides::route('/'),
            'create' => Pages\CreateSiteGuide::route('/create'),
            'edit' => Pages\EditSiteGuide::route('/{record}/edit'),
        ];
    }
}
