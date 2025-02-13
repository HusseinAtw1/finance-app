<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Currency;

class CurrencyPolicy
{

    public function destroy(User $user, Currency $currency): bool
    {
        return $user->id === $currency->user_id;
    }

    public function update(User $user, Currency $currency): bool
    {
        return $user->id === $currency->user_id;
    }

}
