<?php

namespace App\Repositories;



use App\Models\Slide;


class SlideRepository extends Repository
{

    protected  $modelClass = Slide::class;


    public function all()
    {
        return $this->remember(function (){
            return $this->model()->get();
        });
    }


}