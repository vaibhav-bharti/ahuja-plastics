<?php

namespace App\Policies;

use App\Models\ProductionJobReturn;
use App\Models\User;

class ProductionJobReturnPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasModuleAccess('job_returns');
    }

    public function view(User $user, ProductionJobReturn $return): bool
    {
        return $user->hasModuleAccess('job_returns');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ProductionJobReturn $return): bool
    {
        return false;
    }

    public function delete(User $user, ProductionJobReturn $return): bool
    {
        return false;
    }
}
