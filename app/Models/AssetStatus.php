<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetStatus extends Model
{
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
