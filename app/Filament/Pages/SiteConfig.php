<?php

namespace App\Filament\Pages;

use App\Models\Config;
use App\Models\Product;
use App\Repositories\ConfigRepository;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Arr;

class SiteConfig extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.site-config';
    protected static ?string $navigationLabel = '基本設置';
    protected static ?string $title = '基本設置';
    protected static ?string $navigationGroup = '網站設置';
    protected static ?int $navigationSort = 1;

    public function getHeading(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return '基本設置';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $c = Config::pluck('content', 'name')->toArray();
        $g = fn($key, $default = '') => Arr::get($c, $key, $default);

        $this->form->fill([
            // 網站設置
            'site_name' => $g('site_name'),
            'manufactor' => $g('manufactor'),
            'logo_type' => $g('logo_type', '0'),
            'logo' => $g('logo'),
            'logo_svg' => $g('logo_svg'),
            'favicon' => $g('favicon'),
            'foot_text' => $g('foot_text'),
            'copyright' => $g('copyright'),
            'close_site' => $g('close_site', '0'),
            'close_site_tips' => $g('close_site_tips'),
            'pc_m_redirect' => $g('pc_m_redirect', '0'),
            'asset_version' => $g('asset_version'),
            'global_banners' => json_decode($g('global_banners', '[]'), true) ?: [],

            // Google Search
            'robots' => $g('robots'),
            'm_robots' => $g('m_robots'),
            'google_verify_type' => $g('google_verify_type', '1'),
            'google_verify_code' => $g('google_verify_code'),
            'google_verify_file' => $g('google_verify_file'),
            'google_ga' => $g('google_ga'),
            'close_googlebot' => $g('close_googlebot', '0'),
            'googlebot_index_page' => $g('googlebot_index_page'),
            'googlebot_index_page_m' => $g('googlebot_index_page_m'),

            // 運費設置
            'delivery_type' => json_decode($g('delivery_type', '[]'), true) ?: [],
            'freight_where' => $g('freight_where'),
            'freight' => $g('freight'),

            // 產品分組
            ...$this->productGroupFormState($c),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function productGroupFormState(array $config): array
    {
        $state = [];
        $g = fn($key, $default = '') => Arr::get($config, $key, $default);

        foreach ($this->productGroupDefaults() as $key => $defaults) {
            $title = $g("product_group_{$key}_title");
            $intro = $g("product_group_{$key}_intro");
            $productIds = json_decode($g("product_group_{$key}_product_ids", '[]'), true) ?: [];
            $faqs = json_decode($g("product_group_{$key}_faqs", '[]'), true) ?: [];

            $state["product_group_{$key}_title"] = $title !== '' ? $title : $defaults['title'];
            $state["product_group_{$key}_intro"] = $intro !== '' ? $intro : $defaults['intro'];
            $state["product_group_{$key}_product_ids"] = $productIds ?: $defaults['product_ids'];
            $state["product_group_{$key}_faqs"] = $faqs;
        }

        return $state;
    }

    /**
     * @return array<string, array{title: string, intro: string, product_ids: int[]}>
     */
    protected function productGroupDefaults(): array
    {
        return [
            'trial' => [
                'title' => '初次體驗選擇',
                'intro' => '若為首次接觸犀利士Cialis的族群，可從較小盒數組合開始，便於觀察自身反應與適應情況。此類方案適合偶爾需求或評估效果者，能在控制成本的同時，了解犀利士Cialis的實際表現與適合程度。',
                'product_ids' => [11, 12, 13],
            ],
            'recommend' => [
                'title' => '省心推薦專區',
                'intro' => '彙整常見需求與高評價組合，減少逐一比對的時間。適合希望一次掌握熱門選項、在效果與預算之間取得務實平衡的使用族群。',
                'product_ids' => [14, 15, 16, 17],
            ],
            'repurchase' => [
                'title' => '穩定回購專區',
                'intro' => '適合已建立使用節奏、清楚自身需求的族群。以固定週期補貨為取向，兼顧單盒成本與備貨充足度，降低臨時斷貨的不便。',
                'product_ids' => [18, 19, 20],
            ],
            'longterm' => [
                'title' => '長期保養專區',
                'intro' => '面向長期規劃與較大備量需求，透過多盒組合拉低平均成本。適合希望長期備用、降低單顆負擔並減少頻繁下單的使用者。',
                'product_ids' => [21, 22, 23, 24],
            ],
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        $this->tabSite(),
                        $this->tabGoogleSearch(),
                        $this->tabFreight(),
                        $this->tabProductGroups(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function tabSite(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('網站設置')
            ->schema([
                Forms\Components\TextInput::make('site_name')->label('產品名稱'),
                Forms\Components\TextInput::make('manufactor')->label('製造商'),
                Forms\Components\Radio::make('logo_type')
                    ->label('標誌類型')
                    ->options(['0' => '圖片', '1' => 'SVG'])
                    ->default('0')
                    ->live(),
                Forms\Components\FileUpload::make('logo')
                    ->label('標誌')
                    ->directory('site')
                    ->image()
                    ->visible(fn (Forms\Get $get) => $get('logo_type') == '0'),
                Forms\Components\Textarea::make('logo_svg')
                    ->label('標誌 (SVG 代碼)')
                    ->rows(3)
                    ->visible(fn (Forms\Get $get) => $get('logo_type') == '1'),
                Forms\Components\FileUpload::make('favicon')
                    ->label('網站圖標')
                    ->directory('site')
                    ->image(),
                Forms\Components\Textarea::make('foot_text')->label('底部介紹')->rows(3),
                Forms\Components\TextInput::make('copyright')->label('版權所有'),
                Forms\Components\Radio::make('close_site')
                    ->label('關閉網站')
                    ->options(['1' => '關閉', '0' => '開放'])
                    ->default('0'),
                Forms\Components\TextInput::make('close_site_tips')->label('關閉提示'),
                Forms\Components\Radio::make('pc_m_redirect')
                    ->label('PC 移動端跳轉')
                    ->options(['1' => '開啟', '0' => '關閉'])
                    ->default('0'),
                Forms\Components\TextInput::make('asset_version')->label('資源版本號')
                    ->helperText('⚠️ 已廢棄：前端改用 Release Token（release:stamp 生成），此欄位不再生效')
                    ->disabled(),
                Forms\Components\Section::make('頁面頂部背景圖池')
                    ->description('用於產品、新聞等頁面頂部背景；每次重整頁面隨機取一張。')
                    ->schema([
                        Forms\Components\FileUpload::make('global_banners')
                            ->label('背景圖片')
                            ->directory('banners')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->imageEditor()
                            ->helperText('可上傳多張；建議寬圖，例如 1920×600'),
                    ]),
            ]);
    }

    protected function tabFreight(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('運費設置')
            ->schema([
                Forms\Components\CheckboxList::make('delivery_type')
                    ->label('配送方式')
                    ->options([
                        '1' => '快遞宅配 貨到付款',
                        '2' => '超商(7-11) 取貨付款',
                    ]),
                Forms\Components\TextInput::make('freight_where')
                    ->label('商品總價低於')
                    ->placeholder('商品價格低於該數值時將收運費'),
                Forms\Components\TextInput::make('freight')
                    ->label('運費'),
            ]);
    }

    protected function tabGoogleSearch(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Google 搜尋')
            ->schema([
                Forms\Components\Textarea::make('robots')->label('爬蟲規則 (PC)')->rows(10),
                Forms\Components\Textarea::make('m_robots')->label('爬蟲規則 (手機)')->rows(5),
                Forms\Components\Radio::make('google_verify_type')
                    ->label('谷歌驗證方式')
                    ->options(['1' => 'HTML標記', '2' => '文件'])
                    ->default('1')
                    ->live(),
                Forms\Components\TextInput::make('google_verify_code')
                    ->label('HTML 標記驗證')
                    ->placeholder('<meta name="google-site-verification" content="..." />')
                    ->visible(fn (Forms\Get $get) => $get('google_verify_type') == '1'),
                Forms\Components\FileUpload::make('google_verify_file')
                    ->label('谷歌驗證文件')
                    ->directory('google-verify')
                    ->visible(fn (Forms\Get $get) => $get('google_verify_type') == '2'),
                Forms\Components\Textarea::make('google_ga')->label('Google 分析')->rows(10),
                Forms\Components\Radio::make('close_googlebot')
                    ->label('谷歌蜘蛛訪問')
                    ->options(['1' => '禁止', '0' => '開放'])
                    ->default('0'),
                Forms\Components\Textarea::make('googlebot_index_page')->label('谷歌首頁內容')->rows(15),
                Forms\Components\Textarea::make('googlebot_index_page_m')->label('谷歌首頁內容(m)')->rows(15),
            ]);
    }

    protected function tabProductGroups(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('產品分組')
            ->schema([
                Forms\Components\Tabs::make('ProductGroups')
                    ->tabs([
                        $this->productGroupTab('trial', '初次體驗選擇'),
                        $this->productGroupTab('recommend', '省心推薦專區'),
                        $this->productGroupTab('repurchase', '穩定回購專區'),
                        $this->productGroupTab('longterm', '長期保養專區'),
                    ]),
            ]);
    }

    protected function productGroupTab(string $key, string $label): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make($label)
            ->schema([
                Forms\Components\TextInput::make("product_group_{$key}_title")
                    ->label('標題')
                    ->required()
                    ->maxLength(120),
                Forms\Components\Textarea::make("product_group_{$key}_intro")
                    ->label('描述')
                    ->rows(4)
                    ->helperText('顯示在該分組標題下方'),
                Forms\Components\Select::make("product_group_{$key}_product_ids")
                    ->label('包含產品')
                    ->options(
                        Product::query()
                            ->orderByDesc('sort')
                            ->orderBy('id')
                            ->get()
                            ->mapWithKeys(fn(Product $product) => [
                                $product->id => "#{$product->id} {$product->name}" . ($product->quantity ? "（{$product->quantity}盒）" : ''),
                            ])
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->helperText('選擇順序即前台列表順序'),
                Forms\Components\Repeater::make("product_group_{$key}_faqs")
                    ->label('常見問題')
                    ->schema([
                        Forms\Components\TextInput::make('q')->label('問題')->required(),
                        Forms\Components\Textarea::make('a')->label('回答')->rows(3)->required(),
                    ])
                    ->columns(1)
                    ->defaultItems(0)
                    ->addActionLabel('新增常見問題')
                    ->collapsible()
                    ->reorderable()
                    ->itemLabel(fn(array $state): ?string => mb_substr($state['q'] ?? '', 0, 30)),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode(array_values(array_filter($value, fn($v) => $v !== null)));
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
