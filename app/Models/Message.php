<?php

namespace App\Models;
    use Illuminate\Database\Eloquent\Model;
class Message extends Model
{

    protected $fillable = [
        'name','phone','email','content','ip','ipcountry','user_agent','from_domain','source_site','type','sex'
    ];


}
