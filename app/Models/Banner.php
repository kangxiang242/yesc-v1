<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public function descs()
    {
        return $this->hasMany(BannerDesc::class);
    }
}
