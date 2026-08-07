<?php

namespace App\Policies;

use App\Models\Production;
use App\Models\User;

class ProductionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasModuleAccess('production');
    }

    public function view(User $user, Production $production): bool
    {
        return $user->hasModuleAccess('production');
    }

    public function create(User $user): bool
    {
        return $user->hasModuleAccess('production');
    }

    public function update(User $user, Production $production): bool
    {
        return $user->hasModuleAccess('production')
            && $production->created_by === $user->id
            && ! $production->status;
    }

    public function delete(User $user, Production $production): bool
    {
        return $this->update($user, $production);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
