<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AssetStatus;

class AssetStatusPolicy
{
    /**
     * Create a new policy instance.
     */
    public function delete(User $user, AssetStatus $assetStatus)
    {
        return $user->id === $assetStatus->user_id;
    }

    public function update(User $user, AssetStatus $assetStatus)
    {
        return $user->id === $assetStatus->user_id;
    }
}
