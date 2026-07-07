<?php

namespace App\Repositories;

use App\Models\Banner;

class BannerRepository extends Repository
{

    protected  $modelClass = Banner::class;


    public function all()
    {
        return $this->remember(function (){
            return $this->model()->get();
        });
    }

    public function getPageBanner($page,$type=0){
        $banner = $this->all();
        $page = '/'.trim($page,'/');

        return $banner->filter(function($item)use ($page,$type){
            return $item->type == $type && $item->page == $page;
        });



    }



}