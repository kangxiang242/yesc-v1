<?php

namespace App\Filament\Pages;

use App\Filament\Components\WangEditor;
use App\Models\Config;
use App\Repositories\ConfigRepository;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Arr;

class SiteGuidePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.site-guide-page';
    protected static ?string $navigationLabel = '頁面管理';
    protected static ?string $title = '頁面管理';
    protected static ?string $navigationGroup = '網站設置';
    protected static ?int $navigationSort = 2;

    public function getHeading(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return '頁面管理';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $c = Config::pluck('content', 'name')->toArray();
        $g = fn($key, $default = '') => Arr::get($c, $key, $default);

        $this->form->fill([
            // 首页
            'home_about_title' => $g('home_about_title'),
            'home_about_desc' => $g('home_about_desc'),
            'home_banners' => json_decode($g('home_banners', '[]'), true) ?: [],
            'choose' => json_decode($g('choose', '[]'), true) ?: [],
            'taboo' => json_decode($g('taboo', '[]'), true) ?: [],

            // 產品頁面
            'goods_images' => json_decode($g('goods_images', '[]'), true) ?: [],
            'goods_instructions' => json_decode($g('goods_instructions', '[]'), true) ?: [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('網站指南')
                    ->tabs([
                        $this->tabHome(),
                        $this->tabProduct(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function tabHome(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('首页')
            ->schema([
                Forms\Components\Section::make('首屏輪播圖')
                    ->description('對應首頁 hero-slide，最多 4 張；順序即輪播順序。未上傳時使用預設靜態圖。')
                    ->schema([
                        Forms\Components\FileUpload::make('home_banners')
                            ->label('輪播圖片')
                            ->directory('article')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(4)
                            ->imageEditor()
                            ->helperText('建議尺寸約 1920×1080，格式 webp / jpg / png'),
                    ]),

                Forms\Components\TextInput::make('home_about_title')
                    ->label('標題')
                    ->maxLength(255),

                WangEditor::make('home_about_desc')
                    ->label('介紹描述')
                    ->mode('simple'),

                Forms\Components\Section::make('選擇')->collapsible()->schema([
                    Forms\Components\Repeater::make('choose')
                        ->label(false)
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('標題')
                                ->required()
                                ->columnSpan(1),
                            Forms\Components\Textarea::make('desc')
                                ->label('描述')
                                ->rows(3)
                                ->columnSpan(1),
                            Forms\Components\FileUpload::make('img')
                                ->label('圖片')
                                ->directory('images')
                                ->image()
                                ->columnSpan(1),
                        ])
                        ->columns(1)
                        ->defaultItems(0)
                        ->addActionLabel('新增選項')
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['title'] ?? '新選項'),
                ]),

                Forms\Components\Section::make('服用禁忌')->collapsible()->schema([
                    Forms\Components\Repeater::make('taboo')
                        ->label(false)
                        ->schema([
                            Forms\Components\Textarea::make('desc')
                                ->label('描述')
                                ->rows(2)
                                ->columnSpan(1),
                            Forms\Components\FileUpload::make('img')
                                ->label('圖標')
                                ->directory('images')
                                ->image()
                                ->columnSpan(1),
                        ])
                        ->columns(1)
                        ->defaultItems(0)
                        ->addActionLabel('新增禁忌項')
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => mb_substr($state['desc'] ?? '', 0, 20)),
                ]),
            ]);
    }

    protected function tabProduct(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('產品頁面')
            ->schema([
                Forms\Components\Section::make('產品詳情背景圖')
                    ->description('對應產品詳情頁 bg-box 背景輪播；可上傳多張，前台隨機輪播。')
                    ->schema([
                        Forms\Components\FileUpload::make('goods_images')
                            ->label('背景圖片')
                            ->directory('goods')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->imageEditor()
                            ->helperText('建議寬圖，格式 webp / jpg / png'),
                    ]),

                Forms\Components\Section::make('藥品說明')->collapsible()->schema([
                    Forms\Components\Repeater::make('goods_instructions')
                        ->label(false)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('名稱')
                                ->required()
                                ->columnSpan(1),
                            Forms\Components\Textarea::make('value')
                                ->label('內容')
                                ->rows(3)
                                ->columnSpan(1),
                        ])
                        ->columns(1)
                        ->defaultItems(0)
                        ->addActionLabel('新增說明項')
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? '新項目'),
                ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $isFileList = $value === [] || array_is_list($value) && is_string($value[0] ?? null);

                if ($isFileList) {
                    $value = json_encode(
                        array_values(array_filter($value, fn($item) => is_string($item) && $item !== '')),
                        JSON_UNESCAPED_UNICODE
                    );
                } else {
                    $filtered = collect($value)
                        ->filter(fn($item) => !($item['_remove_'] ?? false))
                        ->map(function ($item) {
                            if (is_array($item)) {
                                unset($item['_remove_']);
                            }
                            return $item;
                        })
                        ->values()
                        ->toArray();
                    $value = json_encode($filtered, JSON_UNESCAPED_UNICODE);
                }
            }

            Config::updateOrCreate(
                ['name' => $key],
                ['name' => $key, 'content' => $value ?? '']
            );
        }

        ConfigRepository::make()->forget();

        Notification::make()->title('保存成功')->success()->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('保存')
                ->submit('save'),
        ];
    }
}
