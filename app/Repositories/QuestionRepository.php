<?php

namespace App\Repositories;

use App\Models\Faq;



class QuestionRepository extends Repository
{

    protected  $modelClass = Faq::class;


    public function all()
    {
        return $this->remember(function (){
            return $this->model()->orderBy('sort')->where('status',1)->get();
        });
    }

    public function current(){

        return $this->all()->where('uri',urldecode(request()->path()));
    }


}