<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCateResource\Pages;
use App\Models\ArticleCate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArticleCateResource extends Resource
{
    protected static ?string $model = ArticleCate::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = '文章分類';

    protected static ?string $label = '文章分類';

    protected static ?string $pluralLabel = '文章分類';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('分類名稱')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('sub_name')
                            ->label('副標題')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('uri')
                            ->label('路徑')
                            ->required()
                            ->columnSpan(1)
                            ->helperText('如填寫：news，前端url等於 http://xxx.com/news，請勿隨意更改以免影響SEO'),
                        Forms\Components\Radio::make('status')
                            ->label('狀態')
                            ->options(['1' => '上架', '0' => '下架'])
                            ->default('1')
                            ->required(),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->columnSpan(1)
                            ->helperText('數值越大排序越前')
                            ->required(),
                        Forms\Components\Textarea::make('desc')
                            ->label('描述')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('分類名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('狀態')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => $state ? '正常' : '停用'),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('創建時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('sort', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleCates::route('/'),
            'create' => Pages\CreateArticleCate::route('/create'),
            'edit' => Pages\EditArticleCate::route('/{record}/edit'),
        ];
    }
}
