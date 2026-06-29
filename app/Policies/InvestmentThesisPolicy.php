<?php

namespace App\Policies;

use App\Models\InvestmentThesis;
use App\Models\User;

class InvestmentThesisPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage theses');
    }

    public function view(User $user, InvestmentThesis $investmentThesis): bool
    {
        return $user->can('manage theses');
    }

    public function create(User $user): bool
    {
        return $user->can('manage theses');
    }

    public function update(User $user, InvestmentThesis $investmentThesis): bool
    {
        return $user->can('manage theses');
    }

    public function delete(User $user, InvestmentThesis $investmentThesis): bool
    {
        return $user->can('manage theses');
    }

    public function restore(User $user, InvestmentThesis $investmentThesis): bool
    {
        return $user->can('manage theses');
    }

    public function forceDelete(User $user, InvestmentThesis $investmentThesis): bool
    {
        return $user->can('manage theses');
    }
}
