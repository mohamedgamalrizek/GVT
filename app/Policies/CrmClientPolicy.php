<?php

namespace App\Policies;

use App\Models\CrmClient;
use App\Models\User;

class CrmClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage crm') || $user->can('manage assigned clients');
    }

    public function view(User $user, CrmClient $crmClient): bool
    {
        return $user->can('manage crm') || (int) $crmClient->assigned_to_user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('manage crm') || $user->can('manage assigned clients');
    }

    public function update(User $user, CrmClient $crmClient): bool
    {
        return $this->view($user, $crmClient);
    }

    public function delete(User $user, CrmClient $crmClient): bool
    {
        return $user->can('manage crm');
    }

    public function restore(User $user, CrmClient $crmClient): bool
    {
        return $user->can('manage crm');
    }

    public function forceDelete(User $user, CrmClient $crmClient): bool
    {
        return false;
    }

    public function assign(User $user, CrmClient $crmClient): bool
    {
        return $user->can('manage crm');
    }
}
