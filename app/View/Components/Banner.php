<?php

namespace App\View\Components;

use App\Repositories\BannerRepository;
use Illuminate\View\Component;

class Banner extends Component
{
    public function __construct()
    {
    }

    public function render()
    {
        $banner = app(BannerRepository::class)->getPageBanner(request()->path());

        if ($banner) {
            return view('components.banner', compact('banner'));
        }
    }
}
