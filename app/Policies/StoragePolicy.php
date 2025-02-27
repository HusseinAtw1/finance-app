<?php

namespace App\Policies;

use App\Models\Storage;
use App\Models\User;

class StoragePolicy
{
    public function update(User $user, Storage $storage)
    {
        return $user->id === $storage->user_id;
    }

    public function delete(User $user, Storage $storage)
    {
        return $user->id === $storage->user_id;
    }

    public function view(User $user, Storage $storage)
    {
        return $user->id === $storage->user_id;
    }

    public function create(User $user)
    {
        return true;
    }
}
