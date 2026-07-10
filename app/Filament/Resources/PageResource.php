<?php

namespace App\Filament\Resources;

use App\Filament\Components\WangEditor;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationLabel = '單頁管理';
    protected static ?string $label = '頁面';
    protected static ?string $pluralLabel = '頁面';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('uri')
                        ->label('路徑')
                        ->required()
                        ->columnSpan(1)
                        ->helperText('填寫如：about，則預覽路徑為 https://xxxx.com/about，請勿重複'),
                    Forms\Components\TextInput::make('title')
                        ->label('頁面標題')
                        ->required()
                        ->columnSpan(1),
                    Forms\Components\Textarea::make('desc')
                        ->label('頁面描述')
                        ->rows(2)
                        ->columnSpanFull(),
                    WangEditor::make('content')
                        ->label('內容')
                        ->columnSpanFull(),
                    Forms\Components\ViewField::make('code_helper')
                        ->label('')
                        ->view('forms.components.code-editor-helper')
                        ->columnSpanFull(),
                    Forms\Components\Radio::make('status')
                        ->label('狀態')
                        ->options(['1' => '正常', '0' => '關閉'])
                        ->default('1')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('uri')->label('路徑'),
                Tables\Columns\TextColumn::make('title')->label('標題'),
                Tables\Columns\TextColumn::make('status')
                    ->label('狀態')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => $state ? '正常' : '關閉'),
                Tables\Columns\TextColumn::make('created_at')->label('創建時間')->dateTime('Y-m-d H:i'),
                Tables\Columns\TextColumn::make('updated_at')->label('更新時間')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('uri')->form([Forms\Components\TextInput::make('uri')->label('路徑')])
                    ->query(fn ($query, $data) => $query->when($data['uri'], fn ($q, $v) => $q->where('uri', 'like', "%{$v}%"))),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
