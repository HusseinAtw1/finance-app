<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    Use SoftDeletes;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionInfo()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function  assetCategory()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function assetStatus()
    {
        return $this->belongsTo(AssetStatus::class);
    }

    public function assetValueHistory()
    {
        return $this->hasMany(AssetValueHistory::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
