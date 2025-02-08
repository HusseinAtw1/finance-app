<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetStatus extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'name',
    ];
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
