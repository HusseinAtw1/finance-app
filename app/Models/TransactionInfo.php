<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionInfo extends Model
{
    protected $fillable = [
        'transaction_id',
        'quantity',
        'purchase_price',
        'current_price',
        'sold_for'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }
}

