<?php

namespace App\Policies;

use App\Models\AssetType;
use App\Models\User;

class AssetTypePolicy
{
    /**
     * Create a new policy instance.
     */
    public function delete(User $user, AssetType $assetType)
    {
        return $user->id === $assetType->user_id;
    }

    public function update(User $user, AssetType $assetType)
    {
        return $user->id === $assetType->user_id;
    }
}
