<?php

namespace App\Repositories;


use App\Models\Page;



class PageRepository extends Repository
{

    protected  $modelClass = Page::class;


    public function all()
    {
        return $this->remember(function (){
            return $this->model()->where('status',1)->get();
        });
    }

    public function findToUri($uri){
        return $this->all()->where('uri',$uri)->first();
    }




}