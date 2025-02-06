<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
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
}
