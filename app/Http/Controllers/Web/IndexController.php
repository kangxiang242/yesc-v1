<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\ArticleRepository;
use App\Repositories\BannerRepository;
use App\Repositories\ProductRepository;

class IndexController extends Controller
{
    public function index(
        ProductRepository $productRepository,
        ArticleRepository $newRepository,
    ) {
        $groupProductIds = [
            "trial" => [11, 12, 13],
            "recommend" => [14, 15, 16, 17],
            "repurchase" => [18, 19, 20],
            "longterm" => [21, 22, 23, 24],
        ];

        $products = $productRepository->all();
        $productsById = $products->keyBy("id");

        $groups = [
            "trial" => [
                "title" => "初次體驗選擇",
                "items" => collect($groupProductIds["trial"])
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
            ],
            "recommend" => [
                "title" => "省心推薦專區",
                "items" => collect($groupProductIds["recommend"])
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
            ],
            "repurchase" => [
                "title" => "穩定回購組合",
                "items" => collect($groupProductIds["repurchase"])
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
            ],
            "longterm" => [
                "title" => "長期保養計畫",
                "items" => collect($groupProductIds["longterm"])
                    ->map(fn($id) => $productsById->get($id))
                    ->filter()
                    ->values(),
            ],
        ];

        $effectArticles = $newRepository
            ->all()
            ->where("article_cate_id", 1)
            ->sortByDesc("sort");
        $effectReading = $effectArticles->take(3);
        $healthArticles = $newRepository
            ->all()
            ->where("article_cate_id", 2)
            ->sortByDesc("sort");
        $healthReading = $healthArticles->take(3);
        $sideeffects = $newRepository
            ->all()
            ->where("article_cate_id", 3)
            ->sortByDesc("sort")
            ->take(3);

        $banner = BannerRepository::make()->getPageBanner("/");
        $home_banners = get_setting("home_banners")->toArray();
        foreach ($home_banners as &$image) {
            $image = "/storage/" . $image;
        }

        return template(
            "index",
            compact(
                "products",
                "effectReading",
                "healthReading",
                "sideeffects",
                "banner",
                "groups",
                "home_banners",
            ),
        );
    }
}
