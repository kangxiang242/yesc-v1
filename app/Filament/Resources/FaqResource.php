<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use App\Models\ArticleCate;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'FAQ管理';
    protected static ?string $label = 'FAQ';
    protected static ?string $pluralLabel = 'FAQ';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('FAQ內容')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('問題')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('content')
                        ->label('答案')
                        ->rows(4)
                        ->required()
                        ->helperText('建議80-200字，重要醫療問題請附「何時就醫」提示'),
                    Forms\Components\TextInput::make('sort')
                        ->label('排序')
                        ->numeric()
                        ->default(1)
                        ->helperText('數值越大越靠前'),
                ])->columns(1),

            Forms\Components\Section::make('關聯設定')
                ->description('可選：將FAQ關聯到特定分類或單篇文章')
                ->schema([
                    Forms\Components\Select::make('article_cate_id')
                        ->label('關聯文章分類')
                        ->options(ArticleCate::pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('選擇分類（可選）')
                        ->helperText('關聯後，該分類下的所有文章都會展示此FAQ'),
                    Forms\Components\Select::make('article_id')
                        ->label('關聯單篇文章')
                        ->options(Article::pluck('title', 'id'))
                        ->searchable()
                        ->placeholder('選擇文章（可選）')
                        ->helperText('關聯後，僅該文章詳情頁展示此FAQ'),
                ])->columns(2)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('問題')->searchable(),
                Tables\Columns\TextColumn::make('content')->label('答案')->limit(60),
                Tables\Columns\TextColumn::make('sort')->label('排序')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('創建時間')->dateTime('Y-m-d H:i'),
            ])
            ->defaultSort('sort', 'desc')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
