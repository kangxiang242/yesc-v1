<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = '橫幅管理';

    protected static ?string $label = '橫幅';

    protected static ?string $pluralLabel = '橫幅';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本信息')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('page')
                            ->label('網址路徑')
                            ->required()
                            ->helperText('如：https://xxx.com/cate1 填入 /cate1 表示該頁面的橫幅，首頁填入 /')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('href')
                            ->label('跳轉鏈接')
                            ->columnSpan(1),
                    ]),

                Forms\Components\Section::make('橫幅圖片')
                    ->description('PC 端和手機端的橫幅圖片')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('img')
                                    ->label('PC 端圖片')
                                    ->image()
                                    ->imagePreviewHeight(120)
                                    ->panelLayout('compact')
                                    ->removeUploadedFileButtonPosition('center')
                                    ->directory('banner')
                                    ->required(),
                                Forms\Components\FileUpload::make('m_img')
                                    ->label('手機端圖片')
                                    ->image()
                                    ->imagePreviewHeight(120)
                                    ->panelLayout('compact')
                                    ->removeUploadedFileButtonPosition('center')
                                    ->directory('banner'),
                            ]),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('alt')
                            ->label('圖片描述')
                            ->helperText('用於 SEO 和無障礙訪問'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->label('網址路徑'),
                Tables\Columns\ImageColumn::make('img')
                    ->label('圖片(PC)')
                    ->width(200)
                    ->getStateUsing(function ($record) {
                        $imgs = $record->img;
                        if (is_string($imgs)) {
                            $imgs = json_decode($imgs, true);
                        }
                        return is_array($imgs) ? ($imgs[0] ?? null) : $imgs;
                    })
                    ->action(
                        Tables\Actions\Action::make('view_banner_pc')
                            ->modalHeading('橫幅圖片(PC)')
                            ->modalContent(function ($record) {
                                $imgs = $record->img;
                                if (is_string($imgs)) {
                                    $imgs = json_decode($imgs, true);
                                }
                                $src = is_array($imgs) ? ($imgs[0] ?? null) : $imgs;
                                return new \Illuminate\Support\HtmlString(
                                    '<img src="' . asset('storage/' . $src) . '" style="max-width:100%;height:auto" class="mx-auto" />'
                                );
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('關閉')
                            ->modalWidth('5xl')
                    ),
                Tables\Columns\ImageColumn::make('m_img')
                    ->label('手機圖片')
                    ->width(200)
                    ->getStateUsing(function ($record) {
                        $imgs = $record->m_img;
                        if (is_string($imgs)) {
                            $imgs = json_decode($imgs, true);
                        }
                        return is_array($imgs) ? ($imgs[0] ?? null) : $imgs;
                    })
                    ->action(
                        Tables\Actions\Action::make('view_banner_m')
                            ->modalHeading('橫幅圖片(M)')
                            ->modalContent(function ($record) {
                                $imgs = $record->m_img;
                                if (is_string($imgs)) {
                                    $imgs = json_decode($imgs, true);
                                }
                                $src = is_array($imgs) ? ($imgs[0] ?? null) : $imgs;
                                return new \Illuminate\Support\HtmlString(
                                    '<img src="' . asset('storage/' . $src) . '" style="max-width:100%;height:auto" class="mx-auto" />'
                                );
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('關閉')
                            ->modalWidth('5xl')
                    ),
                Tables\Columns\TextColumn::make('href')
                    ->label('鏈接'),
                Tables\Columns\TextColumn::make('alt')
                    ->label('圖片描述'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('創建時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'asc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
