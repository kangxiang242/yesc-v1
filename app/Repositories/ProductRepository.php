<?php

namespace App\Repositories;


use App\Models\Product;


class ProductRepository extends Repository
{

    protected  $modelClass = Product::class;


    public function all()
    {
        return $this->remember(function (){
            return $this->model()->with('attr')->where('status',1)->orderBy('sort')->get();
        });
    }

    public function find($id){
        return $this->all()->where('id',$id)->first();
    }




}