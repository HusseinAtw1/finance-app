<?php

namespace App\Policies;

use App\Models\AssetCategory;
use App\Models\User;

class AssetCategoryPolicy
{
    public function delete(User $user, AssetCategory $assetCategory)
    {
        return $user->id === $assetCategory->user_id;
    }

    public function update(User $user, AssetCategory $assetCategory)
    {
        return $user->id === $assetCategory->user_id;
    }
}
