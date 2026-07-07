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

        $groupProductIds = [
            "trial" => [11, 12, 13],
            "recommend" => [14, 15, 16, 17],
            "repurchase" => [18, 19, 20],
            "longterm" => [21, 22, 23, 24],
        ];

        $groups = [
            "trial" => [
                "title" => "初次體驗選擇",
                "des" =>
                    "若為首次接觸犀利士Cialis的族群，可從較小盒數組合開始，便於觀察自身反應與適應情況。此類方案適合偶爾需求或評估效果者，能在控制成本的同時，了解犀利士Cialis的實際表現與適合程度。",
                "items" => collect($groupProductIds["trial"])
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
            ],
            "recommend" => [
                "title" => "省心推薦專區",
                "des" =>
                    "彙整常見需求與高評價組合，減少逐一比對的時間。適合希望一次掌握熱門選項、在效果與預算之間取得務實平衡的使用族群。",
                "items" => collect($groupProductIds["recommend"])
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
            ],
            "repurchase" => [
                "title" => "穩定回購專區",
                "des" =>
                    "適合已建立使用節奏、清楚自身需求的族群。以固定週期補貨為取向，兼顧單盒成本與備貨充足度，降低臨時斷貨的不便。",
                "items" => collect($groupProductIds["repurchase"])
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
            ],
            "longterm" => [
                "title" => "長期保養專區",
                "des" =>
                    "面向長期規劃與較大備量需求，透過多盒組合拉低平均成本。適合希望長期備用、降低單顆負擔並減少頻繁下單的使用者。",
                "items" => collect($groupProductIds["longterm"])
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
            ],
        ];

        $trialGroup = $groups["trial"];
        unset($groups["trial"]);
        $groups["trial"] = $trialGroup;

        $hot = $products->isNotEmpty() ? $products->random(3) : collect();

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
}
