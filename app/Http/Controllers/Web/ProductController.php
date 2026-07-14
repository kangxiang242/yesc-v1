<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(ProductRepository $productRepository)
    {
        $products = $productRepository->all();
        $productsById = $products->keyBy("id");

        $groupKeys = ["recommend", "repurchase", "longterm", "trial"];
        $defaults = $this->productGroupDefaults();

        $groups = [];
        foreach ($groupKeys as $key) {
            $default = $defaults[$key];

            $title = trim((string) get_setting("product_group_{$key}_title"));
            $des = trim((string) get_setting("product_group_{$key}_intro"));
            $productIds = get_setting("product_group_{$key}_product_ids")->toArray();
            $faqRows = get_setting("product_group_{$key}_faqs")->toArray();

            $productIds = array_values(array_filter(array_map('intval', $productIds)));
            if ($productIds === []) {
                $productIds = $default["product_ids"];
            }

            $faqs = collect($faqRows)
                ->filter(fn($row) => is_array($row) && filled($row["q"] ?? null) && filled($row["a"] ?? null))
                ->map(fn($row) => (object) [
                    "title" => (string) $row["q"],
                    "content" => (string) $row["a"],
                ])
                ->values();

            $groups[$key] = [
                "key" => $key,
                "title" => $title !== "" ? $title : $default["title"],
                "des" => $des !== "" ? $des : $default["intro"],
                "items" => collect($productIds)
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
                "faqs" => $faqs,
            ];
        }

        $hot = $products->isNotEmpty() ? $products->random(min(3, $products->count())) : collect();

        return template("product.index", compact("products", "hot", "groups"));
    }

    public function show($id)
    {
        $goods = ProductRepository::make()->find($id);

        if (!$goods) {
            abort(404);
        }

        $shop_images[] = storage_url($goods->img);
        $shop_images[] = "/static/v2/img/goods-1.jpg";
        $shop_images[] = "/static/v2/img/goods-2.jpg";
        $shop_images[] = "/static/v2/img/goods-3.jpg";

        $product = $goods;

        $now = Carbon::now();
        $showCountdown = $now->between(
            Carbon::today()->setTime(15, 0, 0),
            Carbon::today()->setTime(16, 59, 59),
        );

        $goods_images = get_setting("goods_images")->toArray();

        foreach ($goods_images as &$image) {
            $image = "/storage/" . $image;
        }

        return template(
            "product.show",
            compact(
                "goods",
                "shop_images",
                "product",
                "showCountdown",
                "goods_images",
            ),
        );
    }

    /**
     * @return array<string, array{title: string, intro: string, product_ids: int[]}>
     */
    protected function productGroupDefaults(): array
    {
        return [
            "trial" => [
                "title" => "初次體驗選擇",
                "intro" =>
                    "若為首次接觸犀利士Cialis的族群，可從較小盒數組合開始，便於觀察自身反應與適應情況。此類方案適合偶爾需求或評估效果者，能在控制成本的同時，了解犀利士Cialis的實際表現與適合程度。",
                "product_ids" => [11, 12, 13],
            ],
            "recommend" => [
                "title" => "省心推薦專區",
                "intro" =>
                    "彙整常見需求與高評價組合，減少逐一比對的時間。適合希望一次掌握熱門選項、在效果與預算之間取得務實平衡的使用族群。",
                "product_ids" => [14, 15, 16, 17],
            ],
            "repurchase" => [
                "title" => "穩定回購專區",
                "intro" =>
                    "適合已建立使用節奏、清楚自身需求的族群。以固定週期補貨為取向，兼顧單盒成本與備貨充足度，降低臨時斷貨的不便。",
                "product_ids" => [18, 19, 20],
            ],
            "longterm" => [
                "title" => "長期保養專區",
                "intro" =>
                    "面向長期規劃與較大備量需求，透過多盒組合拉低平均成本。適合希望長期備用、降低單顆負擔並減少頻繁下單的使用者。",
                "product_ids" => [21, 22, 23, 24],
            ],
        ];
    }
}
