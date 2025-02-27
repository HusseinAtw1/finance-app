<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TransactionDetail;

class TransactionPolicy
{
    public function delete(User $user, TransactionDetail $transactionDetail)
    {
        return $user->id === $transactionDetail->transaction->user_id;
    }
}
