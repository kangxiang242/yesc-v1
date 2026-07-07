<?php

namespace App\Repositories;

use App\Models\Seo;
use Illuminate\Support\Str;

class SeoRepository extends Repository
{

    protected $modelClass = Seo::class;


    public function all()
    {
        return $this->remember(function (){
            return $this->model()->get();
        });
    }

    public function current(){
        $currentPath = '/'.ltrim(request()->path(),'/');
        return $this->all()->where('path',$currentPath)->first();
    }

    public function findPath($path){
        $path = '/'.ltrim($path,'/');
        return $this->all()->where('path',$path)->first();
    }


}