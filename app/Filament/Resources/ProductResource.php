<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = '商品管理';

    protected static ?string $label = '商品';

    protected static ?string $pluralLabel = '商品';
    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('產品名稱')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('img')
                            ->label('圖片(PC)')
                            ->directory('product')
                            ->disk('public')
                            ->image()
                            ->imagePreviewHeight(120)
                            ->panelLayout('compact')
                            ->removeUploadedFileButtonPosition('center')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('價格')
                            ->numeric()
                            ->default(800)
                            ->required(),
                        Forms\Components\TextInput::make('market_price')
                            ->label('市場價')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('quantity')
                            ->label('盒數')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
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
                            ->helperText('數值越大排序越前')
                            ->required(),
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
                    ->label('名稱')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('img')
                    ->label('圖片')
                    ->width(200)
                    ->extraImgAttributes(['class' => 'object-contain max-h-32 w-auto'])
                    ->action(
                        Tables\Actions\Action::make('view_product_img')
                            ->modalHeading('商品圖片')
                            ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString(
                                '<img src="' . asset('storage/' . $record->img) . '" style="max-width:100%;height:auto" class="mx-auto" />'
                            ))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('關閉')
                            ->modalWidth('5xl')
                    ),
                Tables\Columns\TextColumn::make('price')
                    ->label('售價')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('上架'),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
