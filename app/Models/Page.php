<?php

namespace App\Models;
    use Illuminate\Database\Eloquent\Model;
class Page extends Model
{

    protected $fillable = [
        'uri',
        'title',
        'desc',
        'mode',
        'content',
        'html_file',
        'status',
    ];
}
