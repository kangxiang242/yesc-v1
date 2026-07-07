<?php


namespace App\Http\Composers;


use App\Repositories\BannerDescRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SeoRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\SlideRepository;
use Carbon\Carbon;
use Illuminate\View\View;

class LayoutComposer
{
    /**
     * @var SeoRepository
     */
    private $seoRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var BannerDescRepository
     */
    private $bannerDescRepository;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var SlideRepository
     */
    private $slideRepository;


    /**
     * @param SeoRepository $seoRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        SeoRepository $seoRepository,
        ProductRepository $productRepository,
        BannerDescRepository $bannerDescRepository,
        QuestionRepository $questionRepository,
        SlideRepository $slideRepository
    )
    {
        $this->seoRepository = $seoRepository;
        $this->productRepository = $productRepository;
        $this->bannerDescRepository = $bannerDescRepository;
        $this->questionRepository = $questionRepository;
        $this->slideRepository = $slideRepository;
    }


    /**
     *
     * @param View $view
     * @return void
     */
    public function all(View $view){

        $mate = $this->seoRepository->current();

        $view->with('mate',$mate);

        $view->with('buyer_configs',$this->buyerConfig());

        $view->with('period',$this->timeisMorning());

        $allProducts = $this->productRepository->all();
        $view->with('random_product', $allProducts->isNotEmpty() ? $allProducts->random() : null);


        $allBanners = $this->bannerDescRepository->all();
        $global_banner = $allBanners->isNotEmpty() ? $allBanners->random() : new \stdClass();

        if (isset($global_banner->img)) {
            $global_banner->img = $this->globalBanners();
        }
        $view->with('global_banner',$global_banner);

        $faqs = $this->questionRepository->current();

        $view->with('faqs',$faqs);

        $view->with('slides',$this->slideRepository->all());

    }

    /**
     * @return array|string|string[]|null
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function buyerConfig(){
        $buyer_configs = get_setting('buyer_configs');
        $noComments = preg_replace('#//.*#', '', $buyer_configs);
        $singleLine = preg_replace('#\s+#', ' ', $noComments);
        $clean = preg_replace('#,\s*}#', '}', $singleLine);
        return $clean;
    }

    /**
     * @return false|string
     */
    protected function timeisMorning(){
        $now = Carbon::now();
        if ($now->between(
            Carbon::today()->setTime(0, 0, 0),
            Carbon::today()->setTime(16, 59, 59)
        )) {
            return 'morning';
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function globalBanners(){
        $global_banners =  get_setting('global_banners')->toArray();
        $banner = '';
        if($global_banners){
            $banner = storage_url(data_get($global_banners,array_rand($global_banners)));
        } else {
            $banner = '/static/img/center-banner.webp';
        }
        return $banner;
    }





}
