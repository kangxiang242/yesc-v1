<?php

namespace App\Filament\Resources;

use App\Filament\Components\WangEditor;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\ArticleCate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = '文章管理';

    protected static ?string $label = '文章';

    protected static ?string $pluralLabel = '文章';
    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('文章信息')
                    ->schema([
                        Forms\Components\Select::make('article_cate_id')
                            ->label('所屬分類')
                            ->options(ArticleCate::pluck('name', 'id'))
                            ->default(1)
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('title')
                            ->label('標題')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('img')
                            ->label('文章主圖')
                            ->directory('article')
                            ->image()
                            ->disk('public')
                            ->required(),
                        Forms\Components\TextInput::make('img_alt')
                            ->label('圖片描述'),
                        Forms\Components\TextInput::make('read_num')
                            ->label('閱讀數')
                            ->numeric()
                            ->default(1),
                        Forms\Components\Textarea::make('brief')
                            ->label('文章簡介')
                            ->rows(3)
                            ->columnSpanFull(),
                        WangEditor::make('content')
                            ->label('內容')
                            ->columnSpanFull(),
                        Forms\Components\ViewField::make('code_helper')
                            ->label('')
                            ->view('forms.components.code-editor-helper')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('發佈設置')
                    ->schema([
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(1)
                            ->helperText('數值越大排序越前'),
                        Forms\Components\Radio::make('status')
                            ->label('狀態')
                            ->options(['1' => '正常', '0' => '草稿'])
                            ->default('1')
                            ->required(),
                        Forms\Components\Toggle::make('is_recommend')
                            ->label('推薦文章')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('release_at')
                            ->label('發佈時間')
                            ->default(now())
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('SEO 標題'),
                        Forms\Components\TextInput::make('seo_keyword')
                            ->label('SEO 關鍵字'),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO 描述')
                            ->rows(3),
                    ])->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('E-E-A-T 內容責任鏈')
                    ->description('提升醫療類內容可信度：填寫作者、審核者與參考來源資訊')
                    ->schema([
                        Forms\Components\TextInput::make('author_name')
                            ->label('作者姓名')
                            ->placeholder('例：陳XX 藥師'),
                        Forms\Components\Textarea::make('author_bio')
                            ->label('作者簡介')
                            ->placeholder('例：台灣藥學會認證藥師，10年臨床藥學經驗')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('reviewer_name')
                            ->label('醫學審核者')
                            ->placeholder('例：王XX 醫師'),
                        Forms\Components\DateTimePicker::make('reviewed_at')
                            ->label('審核時間'),
                        Forms\Components\Textarea::make('sources')
                            ->label('參考來源')
                            ->placeholder('每行一條，格式：標題|URL（例：美國輝瑞Viagra說明書|https://...）')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('填寫權威機構、藥品說明書、醫學研究等參考來源'),
                        Forms\Components\DateTimePicker::make('last_updated_at')
                            ->label('最後更新時間')
                            ->helperText('更新文章內容時同步更新此欄位'),
                        Forms\Components\Textarea::make('update_summary')
                            ->label('更新摘要')
                            ->placeholder('例：更新了2026年最新副作用資訊')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cate.name')
                    ->label('文章分類')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('標題')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\ImageColumn::make('img')
                    ->label('主圖')
                    ->width(200)
                    ->action(
                        Tables\Actions\Action::make('view_article_img')
                            ->modalHeading('文章主圖')
                            ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString(
                                '<img src="' . asset('storage/' . $record->img) . '" style="max-width:100%;height:auto" class="mx-auto" />'
                            ))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('關閉')
                            ->modalWidth('5xl')
                    ),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('狀態'),
                Tables\Columns\ToggleColumn::make('is_recommend')
                    ->label('推薦'),
                Tables\Columns\TextColumn::make('release_at')
                    ->label('發佈時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('創建時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
