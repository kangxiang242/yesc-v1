<?php

namespace App\Repositories;

use App\Models\ArticleCate;



class ArticleCategoryRepository extends Repository
{

    protected $modelClass = ArticleCate::class;


    public function all()
    {
        return $this->remember(function (){
            return $this->model()->with('article')->where('status',1)->orderBy('sort','desc')->get();
        });
    }



}