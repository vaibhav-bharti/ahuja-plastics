<?php

namespace App\Policies;

use App\Models\ProductionJob;
use App\Models\User;

class ProductionJobPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasModuleAccess('production_jobs');
    }

    public function view(User $user, ProductionJob $job): bool
    {
        return $user->hasModuleAccess('production_jobs');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ProductionJob $job): bool
    {
        return false;
    }

    public function delete(User $user, ProductionJob $job): bool
    {
        return false;
    }
}
